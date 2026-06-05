<?php

declare(strict_types=1);

/*
| Repositorio de comentarios de ticket.
| Maneja las consultas del historial de atencion de cada ticket.
*/
class ComentarioTicketRepository
{
    public function __construct(private PDO $connection)
    {
    }

    public function getAll(array $filters = []): array
    {
        // Orden cronologico para leer el seguimiento como historial.
        $sql = $this->baseSelect() . ' WHERE 1 = 1';
        $params = [];

        foreach (['ticket_id', 'usuario_id', 'estado'] as $filterName) {
            if (!array_key_exists($filterName, $filters)) {
                continue;
            }

            $sql .= sprintf(' AND c.%s = :%s', $filterName, $filterName);
            $params[$filterName] = $filters[$filterName];
        }

        $sql .= ' ORDER BY c.created_at ASC, c.id ASC';

        $statement = $this->connection->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $statement = $this->connection->prepare($this->baseSelect() . ' WHERE c.id = :id');
        $statement->execute(['id' => $id]);

        return $statement->fetch();
    }

    public function create(array $data): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comentarios_ticket (
                ticket_id,
                usuario_id,
                descripcion,
                estado,
                created_by,
                updated_by
            )
             VALUES (
                :ticket_id,
                :usuario_id,
                :descripcion,
                :estado,
                :created_by,
                :updated_by
             )'
        );

        $statement->execute([
            'ticket_id' => $data['ticket_id'],
            'usuario_id' => $data['usuario_id'],
            'descripcion' => $data['descripcion'],
            'estado' => $data['estado'],
            'created_by' => $data['created_by'],
            'updated_by' => $data['updated_by'],
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $statement = $this->connection->prepare(
            'UPDATE comentarios_ticket
             SET ticket_id = :ticket_id,
                 usuario_id = :usuario_id,
                 descripcion = :descripcion,
                 estado = :estado,
                 updated_by = :updated_by
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'ticket_id' => $data['ticket_id'],
            'usuario_id' => $data['usuario_id'],
            'descripcion' => $data['descripcion'],
            'estado' => $data['estado'],
            'updated_by' => $data['updated_by'],
        ]);
    }

    public function delete(int $id, ?int $updatedBy = null): bool
    {
        // Borrado logico para no perder trazabilidad del caso.
        $statement = $this->connection->prepare(
            'UPDATE comentarios_ticket
             SET estado = 0,
                 updated_by = :updated_by
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'updated_by' => $updatedBy,
        ]);
    }

    private function baseSelect(): string
    {
        // Se incluyen datos basicos de ticket y usuario para mostrar el historial.
        return 'SELECT
                    c.*,
                    t.titulo AS ticket_titulo,
                    u.nombre AS usuario_nombre,
                    u.apellido AS usuario_apellido
                FROM comentarios_ticket c
                INNER JOIN tickets t ON t.id = c.ticket_id
                INNER JOIN usuarios u ON u.id = c.usuario_id';
    }
}
