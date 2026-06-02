<?php

declare(strict_types=1);

class EmpleadoService
{
    public function __construct(private EmpleadoRepository $empleadoRepository)
    {
    }

    public function list(): array
    {
        return $this->empleadoRepository->getAll();
    }

    public function getById(int $id): array|false
    {
        return $this->empleadoRepository->findById($id);
    }

    public function create(array $data): array
    {
        try {
            $newEmployeeId = $this->empleadoRepository->create($data);
            return $this->empleadoRepository->findById($newEmployeeId) ?: [];
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('El correo ya existe para otro empleado');
            }

            throw $exception;
        }
    }

    public function update(int $id, array $data): array|false
    {
        if ($this->empleadoRepository->findById($id) === false) {
            return false;
        }

        try {
            $this->empleadoRepository->update($id, $data);
            return $this->empleadoRepository->findById($id);
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('El correo ya existe para otro empleado');
            }

            throw $exception;
        }
    }

    public function delete(int $id): bool
    {
        if ($this->empleadoRepository->findById($id) === false) {
            return false;
        }

        return $this->empleadoRepository->delete($id);
    }
}
