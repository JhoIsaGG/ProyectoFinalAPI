<?php

declare(strict_types=1);

class EstadoTicketRepository
{
    public function __construct(private PDO $connection)
    {
    }

    public function getAll(): array
    {
        $statement = $this->connection->query('SELECT * FROM estados_ticket ORDER BY id DESC');
        return $statement->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $statement = $this->connection->prepare('SELECT * FROM estados_ticket WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->fetch();
    }

    public function create(array $data): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO estados_ticket (nombre, estado, created_by, updated_by)
             VALUES (:nombre, :estado, :created_by, :updated_by)'
        );

        $statement->execute([
            'nombre' => $data['nombre'],
            'estado' => $data['estado'],
            'created_by' => $data['created_by'],
            'updated_by' => $data['updated_by'],
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $statement = $this->connection->prepare(
            'UPDATE estados_ticket
             SET nombre = :nombre,
                 estado = :estado,
                 updated_by = :updated_by
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'nombre' => $data['nombre'],
            'estado' => $data['estado'],
            'updated_by' => $data['updated_by'],
        ]);
    }

    public function delete(int $id, array $data): bool
    {
        $statement = $this->connection->prepare(
            'UPDATE estados_ticket SET estado = 0, updated_by = :updated_by WHERE id = :id'
        );

        return $statement->execute(['id' => $id, 'updated_by' => $data['updated_by']]);
    }
}

