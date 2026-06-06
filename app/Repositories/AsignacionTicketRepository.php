<?php

declare(strict_types=1);

/*
| Repositorio de asignaciones de ticket.
| Encapsula el CRUD sobre la tabla asignaciones_ticket.
*/
class AsignacionTicketRepository
{
    public function __construct(private PDO $connection)
    {
    }

    public function getAll(): array
    {
        $sql = $this->baseSelect() . ' ORDER BY ast.id DESC';
        return $this->connection->query($sql)->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $sql = $this->baseSelect() . ' WHERE ast.id = :id';
        $statement = $this->connection->prepare($sql);
        $statement->execute(['id' => $id]);
        return $statement->fetch();
    }

    public function create(array $data): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO asignaciones_ticket (
                ticket_id,
                agente_id,
                created_by,
                updated_by
            ) VALUES (
                :ticket_id,
                :agente_id,
                :created_by,
                :updated_by
            )'
        );

        $statement->execute([
            'ticket_id' => $data['ticket_id'],
            'agente_id' => $data['agente_id'],
            'created_by' => $data['created_by'],
            'updated_by' => $data['updated_by'] ?? null,
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $statement = $this->connection->prepare(
            'UPDATE asignaciones_ticket
             SET ticket_id = :ticket_id,
                 agente_id = :agente_id,
                 updated_by = :updated_by
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'ticket_id' => $data['ticket_id'],
            'agente_id' => $data['agente_id'],
            'updated_by' => $data['updated_by'],
        ]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->connection->prepare('DELETE FROM asignaciones_ticket WHERE id = :id');
        return $statement->execute(['id' => $id]);
    }

    public function findByAgenteId(int $agenteId): array
    {
        $sql = $this->baseSelect() . ' WHERE ag.usuario_id = :usuario_id ORDER BY ast.id DESC';
        $statement = $this->connection->prepare($sql);
        $statement->execute(['usuario_id' => $agenteId]);
        return $statement->fetchAll();
    }

    private function baseSelect(): string
    {
        return 'SELECT 
                    ag.usuario_id AS usuario_id,
                    ast.id,
                    ast.ticket_id,
                    t.titulo AS ticket_titulo,
                    ast.agente_id,
                    CONCAT(u_agente.nombre, \' \', u_agente.apellido) AS agente_nombre,
                    ast.created_at,
                    ast.updated_at,
                    ast.created_by,
                    CONCAT(u_creator.nombre, \' \', u_creator.apellido) AS creado_por_nombre,
                    ast.updated_by,
                    CONCAT(u_updater.nombre, \' \', u_updater.apellido) AS actualizado_por_nombre
                FROM asignaciones_ticket ast
                INNER JOIN tickets t ON t.id = ast.ticket_id
                INNER JOIN agentes ag ON ag.id = ast.agente_id
                INNER JOIN usuarios u_agente ON u_agente.id = ag.usuario_id
                INNER JOIN usuarios u_creator ON u_creator.id = ast.created_by
                LEFT JOIN usuarios u_updater ON u_updater.id = ast.updated_by';
    }
}
