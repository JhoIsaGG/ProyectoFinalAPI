<?php

declare(strict_types=1);

/*
| Servicio de comentarios de ticket.
| Prepara los datos del historial de atencion antes de llegar a la BD.
*/
class ComentarioTicketService
{
    public function __construct(private ComentarioTicketRepository $comentarioTicketRepository)
    {
    }

    public function list(array $filters = []): array
    {
        return $this->comentarioTicketRepository->getAll($this->normalizeFilters($filters));
    }

    public function getById(int $id): array|false
    {
        return $this->comentarioTicketRepository->findById($id);
    }

    public function create(array $data): array
    {
        try {
            $newCommentId = $this->comentarioTicketRepository->create($this->normalizeCreateData($data));
            return $this->comentarioTicketRepository->findById($newCommentId) ?: [];
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('Algun ticket o usuario no existe');
            }

            throw $exception;
        }
    }

    public function update(int $id, array $data): array|false
    {
        $existingComment = $this->comentarioTicketRepository->findById($id);

        if ($existingComment === false) {
            return false;
        }

        try {
            $this->comentarioTicketRepository->update($id, $this->normalizeUpdateData($existingComment, $data));
            return $this->comentarioTicketRepository->findById($id);
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('Algun ticket o usuario no existe');
            }

            throw $exception;
        }
    }

    public function delete(int $id, ?int $updatedBy = null): bool
    {
        if ($this->comentarioTicketRepository->findById($id) === false) {
            return false;
        }

        try {
            return $this->comentarioTicketRepository->delete($id, $updatedBy);
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('El usuario updated_by no existe');
            }

            throw $exception;
        }
    }

    private function normalizeCreateData(array $data): array
    {
        // Se normalizan relaciones, texto y auditoria para el INSERT.
        return [
            'ticket_id' => (int) $data['ticket_id'],
            'usuario_id' => (int) $data['usuario_id'],
            'descripcion' => trim((string) $data['descripcion']),
            'estado' => $this->normalizeEstado($data['estado'] ?? 1),
            'created_by' => (int) $data['created_by'],
            'updated_by' => $this->normalizeOptionalInteger($data['updated_by'] ?? null),
        ];
    }

    private function normalizeUpdateData(array $existingComment, array $data): array
    {
        // Mantiene valores actuales cuando el update no envia todos los campos.
        return [
            'ticket_id' => array_key_exists('ticket_id', $data) ? (int) $data['ticket_id'] : (int) $existingComment['ticket_id'],
            'usuario_id' => array_key_exists('usuario_id', $data) ? (int) $data['usuario_id'] : (int) $existingComment['usuario_id'],
            'descripcion' => array_key_exists('descripcion', $data) ? trim((string) $data['descripcion']) : $existingComment['descripcion'],
            'estado' => array_key_exists('estado', $data) ? $this->normalizeEstado($data['estado']) : (int) $existingComment['estado'],
            'updated_by' => array_key_exists('updated_by', $data)
                ? $this->normalizeOptionalInteger($data['updated_by'])
                : $this->normalizeOptionalInteger($existingComment['updated_by'] ?? null),
        ];
    }

    private function normalizeFilters(array $filters): array
    {
        // El listado puede filtrarse por ticket, usuario y estado logico.
        $normalizedFilters = [];

        foreach (['ticket_id', 'usuario_id'] as $filterName) {
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
