<?php

declare(strict_types=1);

/*
| Repositorio de categorias de ticket.
| Contiene unicamente las consultas SQL contra categorias_ticket.
*/
class CategoriaTicketRepository
{
    public function __construct(private PDO $connection)
    {
    }

    public function getAll(): array
    {
        $statement = $this->connection->query('SELECT * FROM categorias_ticket ORDER BY id DESC');
        return $statement->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $statement = $this->connection->prepare('SELECT * FROM categorias_ticket WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->fetch();
    }

    public function create(array $data): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO categorias_ticket (nombre, estado, created_by, updated_by)
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
            'UPDATE categorias_ticket
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

    public function delete(int $id, ?int $updatedBy = null): bool
    {
        // Borrado logico: la fila permanece para conservar historial.
        $statement = $this->connection->prepare(
            'UPDATE categorias_ticket
             SET estado = 0,
                 updated_by = :updated_by
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'updated_by' => $updatedBy,
        ]);
    }
}
