<?php

declare(strict_types=1);

class EstadoTicketService
{
    public function __construct(private EstadoTicketRepository $estadoTicketRepository)
    {
    }

    public function list(): array
    {
        return $this->estadoTicketRepository->getAll();
    }

    public function getById(int $id): array|false
    {
        return $this->estadoTicketRepository->findById($id);
    }

    public function create(array $data): array
    {
        try {
            $newTicketStatusId = $this->estadoTicketRepository->create($data);
            return $this->estadoTicketRepository->findById($newTicketStatusId) ?: [];
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('Algun valor relacional no existe');
            }

            throw $exception;
        }
    }

    public function update(int $id, array $data): array|false
    {
        if ($this->estadoTicketRepository->findById($id) === false) {
            return false;
        }

        try {
            $this->estadoTicketRepository->update($id, $data);
            return $this->estadoTicketRepository->findById($id);
        } catch (PDOException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw new RuntimeException('Algun valor relacional no existe');
            }

            throw $exception;
        }
    }

    public function delete(int $id, array $data): bool
    {
        if ($this->estadoTicketRepository->findById($id) === false) {
            return false;
        }

        return $this->estadoTicketRepository->delete($id, $data);
    }
}

