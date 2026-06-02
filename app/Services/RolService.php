<?php

declare(strict_types=1);

class RolService
{
    public function __construct(private RolRepository $rolRepository)
    {
    }

    public function list(): array
    {
        return $this->rolRepository->getAll();
    }

    public function getById(int $id): array|false
    {
        return $this->rolRepository->findById($id);
    }

    public function create(array $data): array
    {
        try {
            $newRoleId = $this->rolRepository->create($data);
            return $this->rolRepository->findById($newRoleId) ?: [];
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('Algun valor relacional no existe');
            }

            throw $exception;
        }
    }

    public function update(int $id, array $data): array|false
    {
        if ($this->rolRepository->findById($id) === false) {
            return false;
        }

        try {
            $this->rolRepository->update($id, $data);
            return $this->rolRepository->findById($id);
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('Algun valor relacional no existe');
            }

            throw $exception;
        }
    }

    public function delete(int $id, array $data): bool
    {
        if ($this->rolRepository->findById($id) === false) {
            return false;
        }

        return $this->rolRepository->delete($id, $data);
    }
}
