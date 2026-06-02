<?php

declare(strict_types=1);

class DepartamentoService
{
    public function __construct(private DepartamentoRepository $departamentoRepository)
    {
    }

    public function list(): array
    {
        return $this->departamentoRepository->getAll();
    }

    public function getById(int $id): array|false
    {
        return $this->departamentoRepository->findById($id);
    }

    public function create(array $data): array
    {
        try {
            $newDepartmentId = $this->departamentoRepository->create($data);
            return $this->departamentoRepository->findById($newDepartmentId) ?: [];
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('Algun valor relacional no existe');
            }

            throw $exception;
        }
    }

    public function update(int $id, array $data): array|false
    {
        if ($this->departamentoRepository->findById($id) === false) {
            return false;
        }

        try {
            $this->departamentoRepository->update($id, $data);
            return $this->departamentoRepository->findById($id);
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('Algun valor relacional no existe');
            }

            throw $exception;
        }
    }

    public function delete(int $id, array $data): bool
    {
        if ($this->departamentoRepository->findById($id) === false) {
            return false;
        }

        return $this->departamentoRepository->delete($id, $data);
    }
}
