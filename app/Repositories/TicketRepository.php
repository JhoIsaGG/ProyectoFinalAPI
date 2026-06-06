<?php

declare(strict_types=1);

/*
| Repositorio de tickets.
| Encapsula las consultas SQL de la solicitud principal de soporte.
*/
class TicketRepository
{
    public function __construct(private PDO $connection)
    {
    }



    public function getAll(array $filters = []): array
    {
        // El SELECT incluye nombres de relaciones para facilitar consumo desde frontend/API.
        $sql = $this->baseSelect() . ' WHERE 1 = 1';
        $params = [];

        foreach ([
            'estado_ticket_id',
            'prioridad_ticket_id',
            'categoria_ticket_id',
            'created_by',
            'estado',
        ] as $filterName) {
            if (!array_key_exists($filterName, $filters)) {
                continue;
            }

            $sql .= sprintf(' AND t.%s = :%s', $filterName, $filterName);
            $params[$filterName] = $filters[$filterName];
        }

        $sql .= ' ORDER BY t.id DESC';

        $statement = $this->connection->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    public function findByUser(int $id): array|false
    {
        $statement = $this->connection->prepare($this->baseSelect() . ' WHERE t.created_by = :id');
        $statement->execute(['id' => $id]);

        // Return all tickets created by the user; empty array if none.
        return $statement->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $statement = $this->connection->prepare($this->baseSelect() . ' WHERE t.id = :id');
        $statement->execute(['id' => $id]);

        return $statement->fetch();
    }

    public function create(array $data): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO tickets (
                titulo,
                descripcion,
                estado_ticket_id,
                prioridad_ticket_id,
                categoria_ticket_id,
                estado,
                created_by,
                updated_by
            )
             VALUES (
                :titulo,
                :descripcion,
                :estado_ticket_id,
                :prioridad_ticket_id,
                :categoria_ticket_id,
                :estado,
                :created_by,
                :updated_by
             )'
        );

        $statement->execute([
            'titulo' => $data['titulo'],
            'descripcion' => $data['descripcion'],
            'estado_ticket_id' => $data['estado_ticket_id'],
            'prioridad_ticket_id' => $data['prioridad_ticket_id'],
            'categoria_ticket_id' => $data['categoria_ticket_id'],
            'estado' => $data['estado'],
            'created_by' => $data['created_by'],
            'updated_by' => $data['updated_by'],
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $statement = $this->connection->prepare(
            'UPDATE tickets
             SET titulo = :titulo,
                 descripcion = :descripcion,
                 estado_ticket_id = :estado_ticket_id,
                 prioridad_ticket_id = :prioridad_ticket_id,
                 categoria_ticket_id = :categoria_ticket_id,
                 estado = :estado,
                 updated_by = :updated_by
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'titulo' => $data['titulo'],
            'descripcion' => $data['descripcion'],
            'estado_ticket_id' => $data['estado_ticket_id'],
            'prioridad_ticket_id' => $data['prioridad_ticket_id'],
            'categoria_ticket_id' => $data['categoria_ticket_id'],
            'estado' => $data['estado'],
            'updated_by' => $data['updated_by'],
        ]);
    }

    public function delete(int $id, ?int $updatedBy = null): bool
    {
        // Borrado logico: estado = 0 marca el ticket como inactivo.
        $statement = $this->connection->prepare(
            'UPDATE tickets
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
        return 'SELECT
                    t.id,
                    t.titulo,
                    t.descripcion,
                    t.estado_ticket_id,
                    t.prioridad_ticket_id,
                    t.categoria_ticket_id,
                    t.estado,
                    t.created_at,
                    t.updated_at,
                    t.created_by,
                    t.updated_by,
                    CONCAT(u_agente.nombre, \' \', u_agente.apellido) AS nombre_apellido_agente_asignado
                FROM tickets t
                LEFT JOIN asignaciones_ticket ast ON ast.ticket_id = t.id
                LEFT JOIN agentes ag ON ag.id = ast.agente_id
                LEFT JOIN usuarios u_agente ON u_agente.id = ag.usuario_id';
    }

    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function rollBack(): void
    {
        if ($this->connection->inTransaction()) {
            $this->connection->rollBack();
        }
    }

    public function getAgentsWithActiveTicketsCount(): array
    {
        $sql = 'SELECT 
                    a.id AS agente_id,
                    a.usuario_id,
                    COALESCE(tc.active_count, 0) AS active_tickets_count
                FROM agentes a
                INNER JOIN usuarios u ON u.id = a.usuario_id
                LEFT JOIN (
                    SELECT 
                        at.agente_id,
                        COUNT(t.id) AS active_count
                    FROM asignaciones_ticket at
                    INNER JOIN tickets t ON t.id = at.ticket_id
                    INNER JOIN estados_ticket et ON et.id = t.estado_ticket_id
                    WHERE t.estado = 1 AND et.nombre != \'Resuelto\'
                    GROUP BY at.agente_id
                ) tc ON tc.agente_id = a.id
                WHERE u.estado = 1
                ORDER BY active_tickets_count ASC';
        return $this->connection->query($sql)->fetchAll();
    }

    public function getAgentCategoriesMap(): array
    {
        $sql = 'SELECT agente_id, categoria_ticket_id FROM agente_categorias';
        $rows = $this->connection->query($sql)->fetchAll();
        $map = [];
        foreach ($rows as $row) {
            $map[(int)$row['agente_id']][] = (int)$row['categoria_ticket_id'];
        }
        return $map;
    }

    public function getPriorityOrder(int $priorityId): int
    {
        $stmt = $this->connection->prepare('SELECT orden FROM prioridades_ticket WHERE id = :id');
        $stmt->execute(['id' => $priorityId]);
        $row = $stmt->fetch();
        return $row ? (int)$row['orden'] : 0;
    }

    public function createAssignment(int $ticketId, int $agenteId, int $createdBy): void
    {
        $stmt = $this->connection->prepare(
            'INSERT INTO asignaciones_ticket (ticket_id, agente_id, created_by)
             VALUES (:ticket_id, :agente_id, :created_by)'
        );
        $stmt->execute([
            'ticket_id' => $ticketId,
            'agente_id' => $agenteId,
            'created_by' => $createdBy
        ]);
    }
}
