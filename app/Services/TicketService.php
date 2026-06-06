<?php

declare(strict_types=1);

/*
| Servicio de tickets.
| Aplica reglas de normalizacion antes de crear, actualizar, listar o eliminar.
*/
class TicketService
{
    public function __construct(
        private TicketRepository $ticketRepository,
        private HistorialTicketRepository $historialTicketRepository
    ) {
    }

  
    public function list(array $filters = []): array
    {
        return $this->ticketRepository->getAll($this->normalizeFilters($filters));
    }

    public function getById(int $id): array|false
    {
        return $this->ticketRepository->findById($id);
    }

      public function getByUser(int $id): array
    {
        return $this->ticketRepository->findByUser($id);
    }


    public function create(array $data): array
    {
        // Verificar existencia de agentes antes de crear el ticket
        $agents = $this->ticketRepository->getAgentsWithActiveTicketsCount();
        if (empty($agents)) {
            // No hay agentes para asignar, abortar creación del ticket
            $this->ticketRepository->rollBack();
            throw new RuntimeException('No hay agentes disponibles para asignar el ticket');
        }
        $this->ticketRepository->beginTransaction();
        try {
            $normalized = $this->normalizeCreateData($data);
            $newTicketId = $this->ticketRepository->create($normalized);

            $this->autoAssignTicket(
                $newTicketId,
                $normalized['categoria_ticket_id'],
                $normalized['prioridad_ticket_id'],
                $normalized['created_by']
            );

            // Registrar estado inicial en el historial
            $this->historialTicketRepository->create([
                'ticket_id' => $newTicketId,
                'estado_ticket_id' => $normalized['estado_ticket_id'],
                'created_by' => $normalized['created_by'],
                'updated_by' => $normalized['updated_by'] ?? null,
            ]);

            $this->ticketRepository->commit();
            return $this->ticketRepository->findById($newTicketId) ?: [];
        } catch (PDOException $exception) {
            $this->ticketRepository->rollBack();
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('Algun estado, prioridad, categoria o usuario no existe');
            }

            throw $exception;
        } catch (Exception $exception) {
            $this->ticketRepository->rollBack();
            throw $exception;
        }
    }

    public function autoAssignTicket(int $ticketId, int $categoryId, int $priorityId, int $createdBy): void
    {
        $priorityOrder = $this->ticketRepository->getPriorityOrder($priorityId);
        $agents = $this->ticketRepository->getAgentsWithActiveTicketsCount();
        $categoriesMap = $this->ticketRepository->getAgentCategoriesMap();

        if (empty($agents)) {
            return;
        }

        $compatibleAgents = [];
        foreach ($agents as $agent) {
            $agentId = (int)$agent['agente_id'];
            $agentCategories = $categoriesMap[$agentId] ?? [];
            if (in_array($categoryId, $agentCategories, true)) {
                $compatibleAgents[] = $agent;
            }
        }

        $assignedAgentId = null;

        if (!empty($compatibleAgents)) {
            $minCompatibleAgent = $compatibleAgents[0];
            $minCompatibleCount = (int)$minCompatibleAgent['active_tickets_count'];

            if ($priorityOrder === 3) {
                $assignedAgentId = (int)$minCompatibleAgent['agente_id'];
            } else {
                if ($minCompatibleCount < 2) {
                    $assignedAgentId = (int)$minCompatibleAgent['agente_id'];
                } else {
                    $assignedAgentId = (int)$agents[0]['agente_id'];
                }
            }
        } else {
            $assignedAgentId = (int)$agents[0]['agente_id'];
        }

        if ($assignedAgentId !== null) {
            $this->ticketRepository->createAssignment($ticketId, $assignedAgentId, $createdBy);
        }
    }

    public function update(int $id, array $data): array|false
    {
        $existingTicket = $this->ticketRepository->findById($id);

        if ($existingTicket === false) {
            return false;
        }

        try {
            $normalizedUpdate = $this->normalizeUpdateData($existingTicket, $data);
            $this->ticketRepository->update($id, $normalizedUpdate);

            // Registrar en historial si el estado cambió
            if ((int) $existingTicket['estado_ticket_id'] !== (int) $normalizedUpdate['estado_ticket_id']) {
                $this->historialTicketRepository->create([
                    'ticket_id' => $id,
                    'estado_ticket_id' => $normalizedUpdate['estado_ticket_id'],
                    'created_by' => $normalizedUpdate['updated_by'] ?? (int) $existingTicket['created_by'],
                    'updated_by' => $normalizedUpdate['updated_by'] ?? null,
                ]);
            }

            return $this->ticketRepository->findById($id);
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('Algun estado, prioridad, categoria o usuario no existe');
            }

            throw $exception;
        }
    }

    public function delete(int $id, ?int $updatedBy = null): bool
    {
        if ($this->ticketRepository->findById($id) === false) {
            return false;
        }

        try {
            return $this->ticketRepository->delete($id, $updatedBy);
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('El usuario updated_by no existe');
            }

            throw $exception;
        }
    }

    private function normalizeCreateData(array $data): array
    {
        // Convierte campos relacionales y de estado a enteros antes del INSERT.
        return [
            'titulo' => trim((string) $data['titulo']),
            'descripcion' => trim((string) $data['descripcion']),
            'estado_ticket_id' => (int) $data['estado_ticket_id'],
            'prioridad_ticket_id' => (int) $data['prioridad_ticket_id'],
            'categoria_ticket_id' => (int) $data['categoria_ticket_id'],
            'estado' => $this->normalizeEstado($data['estado'] ?? 1),
            'created_by' => (int) $data['created_by'],
            'updated_by' => $this->normalizeOptionalInteger($data['updated_by'] ?? null),
        ];
    }

    private function normalizeUpdateData(array $existingTicket, array $data): array
    {
        // Permite updates parciales sin borrar datos que no fueron enviados.
        return [
            'titulo' => array_key_exists('titulo', $data) ? trim((string) $data['titulo']) : $existingTicket['titulo'],
            'descripcion' => array_key_exists('descripcion', $data) ? trim((string) $data['descripcion']) : $existingTicket['descripcion'],
            'estado_ticket_id' => array_key_exists('estado_ticket_id', $data) ? (int) $data['estado_ticket_id'] : (int) $existingTicket['estado_ticket_id'],
            'prioridad_ticket_id' => array_key_exists('prioridad_ticket_id', $data) ? (int) $data['prioridad_ticket_id'] : (int) $existingTicket['prioridad_ticket_id'],
            'categoria_ticket_id' => array_key_exists('categoria_ticket_id', $data) ? (int) $data['categoria_ticket_id'] : (int) $existingTicket['categoria_ticket_id'],
            'estado' => array_key_exists('estado', $data) ? $this->normalizeEstado($data['estado']) : (int) $existingTicket['estado'],
            'updated_by' => array_key_exists('updated_by', $data)
                ? $this->normalizeOptionalInteger($data['updated_by'])
                : $this->normalizeOptionalInteger($existingTicket['updated_by'] ?? null),
        ];
    }

    private function normalizeFilters(array $filters): array
    {
        // Solo se aceptan filtros definidos para evitar condiciones SQL arbitrarias.
        $allowedIntegerFilters = [
            'estado_ticket_id',
            'prioridad_ticket_id',
            'categoria_ticket_id',
            'created_by',
        ];
        $normalizedFilters = [];

        foreach ($allowedIntegerFilters as $filterName) {
            if (isset($filters[$filterName]) && $filters[$filterName] !== '') {
                $normalizedFilters[$filterName] = (int) $filters[$filterName];
            }
        }

        if (array_key_exists('estado', $filters) && $filters['estado'] !== '') {
            $normalizedFilters['estado'] = $this->normalizeEstado($filters['estado']);
        }

        return $normalizedFilters;
    }

    private function normalizeEstado(mixed $value): int
    {
        return (int) $value === 1 ? 1 : 0;
    }

    private function normalizeOptionalInteger(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }
}
