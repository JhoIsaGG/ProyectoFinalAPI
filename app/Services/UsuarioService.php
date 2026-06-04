<?php

declare(strict_types=1);

class UsuarioService
{
    public function __construct(private UsuarioRepository $usuarioRepository)
    {
    }

    public function list(): array
    {
        return $this->usuarioRepository->getAll();
    }

    public function getById(int $id): array|false
    {
        return $this->usuarioRepository->findById($id);
    }

    public function create(array $data): array
    {
        try {
            $normalizedData = $this->normalizeCreateData($data);
            $newUserId = $this->usuarioRepository->create($normalizedData);
            return $this->usuarioRepository->findById($newUserId) ?: [];
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('El correo ya existe para otro usuario, algun valor relacional no existe o categorias invalidas');
            }

            throw $exception;
        }
    }

    public function update(int $id, array $data): array|false
    {
        $existingUser = $this->usuarioRepository->findById($id);
        if ($existingUser === false) {
            return false;
        }

        try {
            $this->usuarioRepository->update($id, $this->normalizeUpdateData($existingUser, $data));
            return $this->usuarioRepository->findById($id);
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('El correo ya existe para otro usuario, algun valor relacional no existe o categorias invalidas');
            }

            throw $exception;
        }
    }

    public function delete(int $id, ?array $data = null): bool
    {
        if ($this->usuarioRepository->findById($id) === false) {
            return false;
        }

        $data = $data ?? ['updated_by' => null];
        return $this->usuarioRepository->delete($id, $data);
    }

    private function normalizeCreateData(array $data): array
    {
        return [
            'nombre' => trim((string) ($data['nombre'] ?? '')),
            'apellido' => trim((string) ($data['apellido'] ?? '')),
            'email' => trim((string) ($data['email'] ?? '')),
            'telefono' => trim((string) ($data['telefono'] ?? '')),
            'password' => (string) ($data['password'] ?? ''),
            'rol_id' => (int) $data['rol_id'],
            'departamento_id' => (int) $data['departamento_id'],
            'estado' => (int) ($data['estado'] ?? 1) === 1 ? 1 : 0,
            'created_by' => isset($data['created_by']) && $data['created_by'] !== '' ? (int) $data['created_by'] : null,
            'updated_by' => isset($data['updated_by']) && $data['updated_by'] !== '' ? (int) $data['updated_by'] : null,
            'categoria_ticket_id' => isset($data['categoria_ticket_id']) ? array_map('intval', (array) $data['categoria_ticket_id']) : [],
        ];
    }

    private function normalizeUpdateData(array $existingUser, array $data): array
    {
        return [
            'nombre' => array_key_exists('nombre', $data) ? trim((string) $data['nombre']) : $existingUser['nombre'],
            'apellido' => array_key_exists('apellido', $data) ? trim((string) $data['apellido']) : $existingUser['apellido'],
            'email' => array_key_exists('email', $data) ? trim((string) $data['email']) : $existingUser['email'],
            'telefono' => array_key_exists('telefono', $data) ? trim((string) $data['telefono']) : $existingUser['telefono'],
            'password' => array_key_exists('password', $data) ? (string) $data['password'] : $existingUser['password'],
            'rol_id' => array_key_exists('rol_id', $data) ? (int) $data['rol_id'] : (int) $existingUser['rol_id'],
            'departamento_id' => array_key_exists('departamento_id', $data) ? (int) $data['departamento_id'] : (int) $existingUser['departamento_id'],
            'estado' => array_key_exists('estado', $data) ? ((int) $data['estado'] === 1 ? 1 : 0) : (int) $existingUser['estado'],
            'updated_by' => isset($data['updated_by']) && $data['updated_by'] !== '' ? (int) $data['updated_by'] : null,
            'categoria_ticket_id' => array_key_exists('categoria_ticket_id', $data) 
                ? array_map('intval', (array) $data['categoria_ticket_id']) 
                : ($existingUser['categoria_ticket_id'] ?? []),
        ];
    }
}
