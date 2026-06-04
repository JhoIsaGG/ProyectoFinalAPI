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
        // Consulta base reutilizable para listar y obtener detalle con datos relacionados.
        return 'SELECT
                    t.*,
                    et.nombre AS estado_ticket_nombre,
                    pt.nombre AS prioridad_ticket_nombre,
                    ct.nombre AS categoria_ticket_nombre,
                    creador.nombre AS creado_por_nombre,
                    creador.apellido AS creado_por_apellido,
                    actualizador.nombre AS actualizado_por_nombre,
                    actualizador.apellido AS actualizado_por_apellido
                FROM tickets t
                INNER JOIN estados_ticket et ON et.id = t.estado_ticket_id
                INNER JOIN prioridades_ticket pt ON pt.id = t.prioridad_ticket_id
                INNER JOIN categorias_ticket ct ON ct.id = t.categoria_ticket_id
                INNER JOIN usuarios creador ON creador.id = t.created_by
                LEFT JOIN usuarios actualizador ON actualizador.id = t.updated_by';
    }
}
