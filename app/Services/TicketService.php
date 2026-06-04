<?php

declare(strict_types=1);

/*
| Servicio de tickets.
| Aplica reglas de normalizacion antes de crear, actualizar, listar o eliminar.
*/
class TicketService
{
    public function __construct(private TicketRepository $ticketRepository)
    {
    }

    public function list(array $filters = []): array
    {
        return $this->ticketRepository->getAll($this->normalizeFilters($filters));
    }

    public function getById(int $id): array|false
    {
        return $this->ticketRepository->findById($id);
    }

    public function create(array $data): array
    {
        try {
            $newTicketId = $this->ticketRepository->create($this->normalizeCreateData($data));
            return $this->ticketRepository->findById($newTicketId) ?: [];
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('Algun estado, prioridad, categoria o usuario no existe');
            }

            throw $exception;
        }
    }

    public function update(int $id, array $data): array|false
    {
        $existingTicket = $this->ticketRepository->findById($id);

        if ($existingTicket === false) {
            return false;
        }

        try {
            $this->ticketRepository->update($id, $this->normalizeUpdateData($existingTicket, $data));
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
