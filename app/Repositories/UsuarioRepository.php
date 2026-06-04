<?php

declare(strict_types=1);

class UsuarioRepository
{
    public function __construct(private PDO $connection)
    {
    }

    public function getAll(): array
    {
        $statement = $this->connection->query('SELECT * FROM usuarios ORDER BY id DESC');
        $users = $statement->fetchAll();

        foreach ($users as &$user) {
            if ((int)$user['rol_id'] === 2) {
                $user['categoria_ticket_id'] = $this->getAgentCategories((int)$user['id']);
            }
        }

        return $users;
    }

    public function findById(int $id): array|false
    {
        $statement = $this->connection->prepare('SELECT * FROM usuarios WHERE id = :id');
        $statement->execute(['id' => $id]);

        $user = $statement->fetch();
        if ($user === false) {
            return false;
        }

        if ((int)$user['rol_id'] === 2) {
            $user['categoria_ticket_id'] = $this->getAgentCategories($id);
        }

        return $user;
    }

    public function create(array $data): int
    {
        $this->connection->beginTransaction();
        try {
            $statement = $this->connection->prepare(
                'INSERT INTO usuarios (nombre, apellido, email, telefono, password, rol_id, departamento_id, estado, created_by, updated_by)
                 VALUES (:nombre, :apellido, :email, :telefono, :password, :rol_id, :departamento_id, :estado, :created_by, :updated_by)'
            );

            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

            $statement->execute([
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'email' => $data['email'],
                'telefono' => $data['telefono'],
                'password' => $passwordHash,
                'rol_id' => $data['rol_id'],
                'departamento_id' => $data['departamento_id'],
                'estado' => $data['estado'],
                'created_by' => $data['created_by'],
                'updated_by' => $data['updated_by'],
            ]);

            $newUserId = (int) $this->connection->lastInsertId();

            if ((int) $data['rol_id'] === 2) {
                $agentStmt = $this->connection->prepare(
                    'INSERT INTO agentes (usuario_id, created_by, updated_by)
                     VALUES (:usuario_id, :created_by, :updated_by)'
                );

                $createdBy = $data['created_by'] !== null ? (int) $data['created_by'] : $newUserId;
                $updatedBy = $data['updated_by'] !== null ? (int) $data['updated_by'] : null;

                $agentStmt->execute([
                    'usuario_id' => $newUserId,
                    'created_by' => $createdBy,
                    'updated_by' => $updatedBy,
                ]);

                $newAgentId = (int) $this->connection->lastInsertId();

                $categories = $data['categoria_ticket_id'] ?? [];
                if (is_array($categories) && count($categories) > 0) {
                    $catStmt = $this->connection->prepare(
                        'INSERT INTO agente_categorias (agente_id, categoria_ticket_id)
                         VALUES (:agente_id, :categoria_ticket_id)'
                    );

                    foreach ($categories as $catId) {
                        $catStmt->execute([
                            'agente_id' => $newAgentId,
                            'categoria_ticket_id' => (int) $catId,
                        ]);
                    }
                }
            }

            $this->connection->commit();
            return $newUserId;
        } catch (Exception $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }
            throw $exception;
        }
    }

    public function update(int $id, array $data): bool
    {
        $this->connection->beginTransaction();
        try {
            $statement = $this->connection->prepare(
                'UPDATE usuarios
                 SET nombre = :nombre,
                     apellido = :apellido,
                     email = :email,
                     telefono = :telefono,
                     password = :password,
                     rol_id = :rol_id,
                     departamento_id = :departamento_id,
                     estado = :estado,
                     updated_by = :updated_by
                 WHERE id = :id'
            );

            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

            $executed = $statement->execute([
                'id' => $id,
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'email' => $data['email'],
                'telefono' => $data['telefono'],
                'password' => $passwordHash,
                'rol_id' => $data['rol_id'],
                'departamento_id' => $data['departamento_id'],
                'estado' => $data['estado'],
                'updated_by' => $data['updated_by'],
            ]);

            if (!$executed) {
                $this->connection->rollBack();
                return false;
            }

            if ((int)$data['rol_id'] === 2) {
                $agentStmt = $this->connection->prepare('SELECT id FROM agentes WHERE usuario_id = :usuario_id');
                $agentStmt->execute(['usuario_id' => $id]);
                $agent = $agentStmt->fetch();

                if ($agent === false) {
                    $insertAgentStmt = $this->connection->prepare(
                        'INSERT INTO agentes (usuario_id, created_by, updated_by)
                         VALUES (:usuario_id, :created_by, :updated_by)'
                    );
                    $createdBy = $data['updated_by'] !== null ? (int)$data['updated_by'] : $id;
                    $insertAgentStmt->execute([
                        'usuario_id' => $id,
                        'created_by' => $createdBy,
                        'updated_by' => $data['updated_by'] ?? null,
                    ]);
                    $agentId = (int)$this->connection->lastInsertId();
                } else {
                    $agentId = (int)$agent['id'];
                    $updateAgentStmt = $this->connection->prepare(
                        'UPDATE agentes SET updated_by = :updated_by WHERE id = :id'
                    );
                    $updateAgentStmt->execute([
                        'id' => $agentId,
                        'updated_by' => $data['updated_by'] ?? null,
                    ]);
                }

                $deleteCatStmt = $this->connection->prepare('DELETE FROM agente_categorias WHERE agente_id = :agente_id');
                $deleteCatStmt->execute(['agente_id' => $agentId]);

                $categories = $data['categoria_ticket_id'] ?? [];
                if (is_array($categories) && count($categories) > 0) {
                    $insertCatStmt = $this->connection->prepare(
                        'INSERT INTO agente_categorias (agente_id, categoria_ticket_id)
                         VALUES (:agente_id, :categoria_ticket_id)'
                    );
                    foreach ($categories as $catId) {
                        $insertCatStmt->execute([
                            'agente_id' => $agentId,
                            'categoria_ticket_id' => (int)$catId,
                        ]);
                    }
                }
            } else {
                $agentStmt = $this->connection->prepare('SELECT id FROM agentes WHERE usuario_id = :usuario_id');
                $agentStmt->execute(['usuario_id' => $id]);
                $agent = $agentStmt->fetch();

                if ($agent !== false) {
                    $agentId = (int)$agent['id'];
                    $deleteCatStmt = $this->connection->prepare('DELETE FROM agente_categorias WHERE agente_id = :agente_id');
                    $deleteCatStmt->execute(['agente_id' => $agentId]);
                }
            }

            $this->connection->commit();
            return true;
        } catch (Exception $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }
            throw $exception;
        }
    }

    private function getAgentCategories(int $usuarioId): array
    {
        $statement = $this->connection->prepare(
            'SELECT ac.categoria_ticket_id 
             FROM agente_categorias ac
             INNER JOIN agentes a ON a.id = ac.agente_id
             WHERE a.usuario_id = :usuario_id'
        );
        $statement->execute(['usuario_id' => $usuarioId]);
        return array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN) ?: []);
    }

    public function delete(int $id, array $data): bool
    {
        $statement = $this->connection->prepare('UPDATE usuarios SET estado = 0, updated_by = :updated_by WHERE id = :id');
        return $statement->execute(['id' => $id, 'updated_by' => $data['updated_by']]);
    }
}
