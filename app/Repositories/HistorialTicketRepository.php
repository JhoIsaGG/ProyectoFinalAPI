<?php

declare(strict_types=1);

/*
| Repositorio de historial de tickets.
| Registra y consulta la auditoría de cambios de estado sobre los tickets.
*/
class HistorialTicketRepository
{
    public function __construct(private PDO $connection)
    {
    }

    public function getAll(): array
    {
        $sql = $this->baseSelect() . ' ORDER BY ht.id DESC';
        $statement = $this->connection->query($sql);
        return $statement->fetchAll();
    }

    public function findByTicketId(int $ticketId): array
    {
        $sql = $this->baseSelect() . ' WHERE ht.ticket_id = :ticket_id ORDER BY ht.id DESC';
        $statement = $this->connection->prepare($sql);
        $statement->execute(['ticket_id' => $ticketId]);
        return $statement->fetchAll();
    }

    public function create(array $data): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO historial_tickets (
                ticket_id,
                estado_ticket_id,
                created_by,
                updated_by
            ) VALUES (
                :ticket_id,
                :estado_ticket_id,
                :created_by,
                :updated_by
            )'
        );

        $statement->execute([
            'ticket_id' => $data['ticket_id'],
            'estado_ticket_id' => $data['estado_ticket_id'],
            'created_by' => $data['created_by'],
            'updated_by' => $data['updated_by'] ?? null,
        ]);

        return (int) $this->connection->lastInsertId();
    }

    private function baseSelect(): string
    {
        return 'SELECT 
                    ht.id,
                    ht.ticket_id,
                    t.titulo AS ticket_titulo,
                    ht.estado_ticket_id,
                    et.nombre AS estado_nombre,
                    ht.created_at,
                    ht.updated_at,
                    ht.created_by,
                    CONCAT(u_creator.nombre, \' \', u_creator.apellido) AS creado_por_nombre,
                    ht.updated_by,
                    CONCAT(u_updater.nombre, \' \', u_updater.apellido) AS actualizado_por_nombre
                FROM historial_tickets ht
                INNER JOIN tickets t ON t.id = ht.ticket_id
                INNER JOIN estados_ticket et ON et.id = ht.estado_ticket_id
                INNER JOIN usuarios u_creator ON u_creator.id = ht.created_by
                LEFT JOIN usuarios u_updater ON u_updater.id = ht.updated_by';
    }
}
