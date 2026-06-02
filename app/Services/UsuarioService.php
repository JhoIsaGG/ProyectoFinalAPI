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
            $newUserId = $this->usuarioRepository->create($data);
            return $this->usuarioRepository->findById($newUserId) ?: [];
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('El correo ya existe para otro usuario o algun valor relacional no existe');
            }

            throw $exception;
        }
    }

    public function update(int $id, array $data): array|false
    {
        if ($this->usuarioRepository->findById($id) === false) {
            return false;
        }

        try {
            $this->usuarioRepository->update($id, $data);
            return $this->usuarioRepository->findById($id);
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('El correo ya existe para otro usuario o algun valor relacional no existe');
            }

            throw $exception;
        }
    }

    public function delete(int $id, array $data): bool
    {
        if ($this->usuarioRepository->findById($id) === false) {
            return false;
        }

        return $this->usuarioRepository->delete($id, $data);
    }
}
