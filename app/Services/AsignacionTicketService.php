<?php

declare(strict_types=1);

/*
| Servicio de asignaciones de ticket.
| Aplica la normalización y validaciones de negocio antes de realizar operaciones de CRUD.
*/
class AsignacionTicketService
{
    public function __construct(private AsignacionTicketRepository $asignacionTicketRepository)
    {
    }

    public function list(): array
    {
        return $this->asignacionTicketRepository->getAll();
    }

    public function getById(int $id): array|false
    {
        return $this->asignacionTicketRepository->findById($id);
    }

    public function create(array $data): array
    {
        try {
            $normalized = $this->normalizeCreateData($data);
            $newId = $this->asignacionTicketRepository->create($normalized);
            return $this->asignacionTicketRepository->findById($newId) ?: [];
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('El ticket, agente o usuario creador no existe en el sistema');
            }
            throw $exception;
        }
    }

    public function update(int $id, array $data): array|false
    {
        $existing = $this->asignacionTicketRepository->findById($id);
        if ($existing === false) {
            return false;
        }

        try {
            $normalized = $this->normalizeUpdateData($existing, $data);
            $this->asignacionTicketRepository->update($id, $normalized);
            return $this->asignacionTicketRepository->findById($id);
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('El ticket, agente o usuario actualizador no existe en el sistema');
            }
            throw $exception;
        }
    }

    public function delete(int $id): bool
    {
        if ($this->asignacionTicketRepository->findById($id) === false) {
            return false;
        }
        return $this->asignacionTicketRepository->delete($id);
    }

    public function getByUser(int $userId): array
    {
        return $this->asignacionTicketRepository->findByAgenteId($userId);
    }

    private function normalizeCreateData(array $data): array
    {
        return [
            'ticket_id' => (int) $data['ticket_id'],
            'agente_id' => (int) $data['agente_id'],
            'created_by' => (int) $data['created_by'],
            'updated_by' => isset($data['updated_by']) ? (int) $data['updated_by'] : null,
        ];
    }

    private function normalizeUpdateData(array $existing, array $data): array
    {
        return [
            'ticket_id' => array_key_exists('ticket_id', $data) ? (int) $data['ticket_id'] : (int) $existing['ticket_id'],
            'agente_id' => array_key_exists('agente_id', $data) ? (int) $data['agente_id'] : (int) $existing['agente_id'],
            'updated_by' => (int) $data['updated_by'], // updated_by es obligatorio para realizar modificaciones
        ];
    }
}
