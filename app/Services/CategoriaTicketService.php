<?php

declare(strict_types=1);

/*
| Servicio de categorias de ticket.
| Centraliza normalizacion de datos y manejo de errores relacionales.
*/
class CategoriaTicketService
{
    public function __construct(private CategoriaTicketRepository $categoriaTicketRepository)
    {
    }

    public function list(): array
    {
        return $this->categoriaTicketRepository->getAll();
    }

    public function getById(int $id): array|false
    {
        return $this->categoriaTicketRepository->findById($id);
    }

    public function create(array $data): array
    {
        try {
            $newCategoryId = $this->categoriaTicketRepository->create($this->normalizeCreateData($data));
            return $this->categoriaTicketRepository->findById($newCategoryId) ?: [];
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('Algun usuario de auditoria no existe');
            }

            throw $exception;
        }
    }

    public function update(int $id, array $data): array|false
    {
        $existingCategory = $this->categoriaTicketRepository->findById($id);

        if ($existingCategory === false) {
            return false;
        }

        try {
            $this->categoriaTicketRepository->update($id, $this->normalizeUpdateData($existingCategory, $data));
            return $this->categoriaTicketRepository->findById($id);
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('Algun usuario de auditoria no existe');
            }

            throw $exception;
        }
    }

    public function delete(int $id, ?int $updatedBy = null): bool
    {
        if ($this->categoriaTicketRepository->findById($id) === false) {
            return false;
        }

        try {
            return $this->categoriaTicketRepository->delete($id, $updatedBy);
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('El usuario updated_by no existe');
            }

            throw $exception;
        }
    }

    private function normalizeCreateData(array $data): array
    {
        // Se asegura que los tipos enviados al repositorio coincidan con la tabla.
        return [
            'nombre' => trim((string) $data['nombre']),
            'estado' => $this->normalizeEstado($data['estado'] ?? 1),
            'created_by' => (int) $data['created_by'],
            'updated_by' => $this->normalizeOptionalInteger($data['updated_by'] ?? null),
        ];
    }

    private function normalizeUpdateData(array $existingCategory, array $data): array
    {
        // En update se conservan los valores existentes cuando no vienen en el payload.
        return [
            'nombre' => array_key_exists('nombre', $data) ? trim((string) $data['nombre']) : $existingCategory['nombre'],
            'estado' => array_key_exists('estado', $data) ? $this->normalizeEstado($data['estado']) : (int) $existingCategory['estado'],
            'updated_by' => array_key_exists('updated_by', $data)
                ? $this->normalizeOptionalInteger($data['updated_by'])
                : $this->normalizeOptionalInteger($existingCategory['updated_by'] ?? null),
        ];
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
