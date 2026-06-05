<?php

declare(strict_types=1);

class PrioridadTicketRepository
{
    public function __construct(private PDO $connection)
    {
    }

    public function getAll(): array
    {
        $statement = $this->connection->query('SELECT * FROM prioridades_ticket ORDER BY orden ASC, id DESC');
        return $statement->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $statement = $this->connection->prepare('SELECT * FROM prioridades_ticket WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->fetch();
    }

    public function create(array $data): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO prioridades_ticket (orden, nombre, estado, created_by, updated_by)
             VALUES (:orden, :nombre, :estado, :created_by, :updated_by)'
        );

        $statement->execute([
            'orden' => $data['orden'],
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
            'UPDATE prioridades_ticket
             SET orden = :orden,
                 nombre = :nombre,
                 estado = :estado,
                 updated_by = :updated_by
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'orden' => $data['orden'],
            'nombre' => $data['nombre'],
            'estado' => $data['estado'],
            'updated_by' => $data['updated_by'],
        ]);
    }

    public function delete(int $id, array $data): bool
    {
        $statement = $this->connection->prepare(
            'UPDATE prioridades_ticket SET estado = 0, updated_by = :updated_by WHERE id = :id'
        );

        return $statement->execute(['id' => $id, 'updated_by' => $data['updated_by']]);
    }
}

