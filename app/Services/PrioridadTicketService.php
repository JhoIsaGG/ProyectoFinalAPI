<?php

declare(strict_types=1);

class PrioridadTicketService
{
    public function __construct(private PrioridadTicketRepository $prioridadTicketRepository)
    {
    }

    public function list(): array
    {
        return $this->prioridadTicketRepository->getAll();
    }

    public function getById(int $id): array|false
    {
        return $this->prioridadTicketRepository->findById($id);
    }

    public function create(array $data): array
    {
        try {
            $newTicketPriorityId = $this->prioridadTicketRepository->create($data);
            return $this->prioridadTicketRepository->findById($newTicketPriorityId) ?: [];
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('Algun valor relacional no existe');
            }

            throw $exception;
        }
    }

    public function update(int $id, array $data): array|false
    {
        if ($this->prioridadTicketRepository->findById($id) === false) {
            return false;
        }

        try {
            $this->prioridadTicketRepository->update($id, $data);
            return $this->prioridadTicketRepository->findById($id);
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('Algun valor relacional no existe');
            }

            throw $exception;
        }
    }

    public function delete(int $id, array $data): bool
    {
        if ($this->prioridadTicketRepository->findById($id) === false) {
            return false;
        }

        return $this->prioridadTicketRepository->delete($id, $data);
    }
}

