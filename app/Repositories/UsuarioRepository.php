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
        return $statement->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $statement = $this->connection->prepare('SELECT * FROM usuarios WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->fetch();
    }

    public function create(array $data): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO usuarios (nombre, apellido, email, telefono, password, rol_id, departamento_id, estado, created_by, updated_by)
             VALUES (:nombre, :apellido, :email, :telefono, :password, :rol_id, :departamento_id, :estado, :created_by, :updated_by)'
        );

        $statement->execute([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'email' => $data['email'],
            'telefono' => $data['telefono'],
            'password' => $data['password'],
            'rol_id' => $data['rol_id'],
            'departamento_id' => $data['departamento_id'],
            'estado' => $data['estado'],
            'created_by' => $data['created_by'],
            'updated_by' => $data['updated_by'],
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
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

        return $statement->execute([
            'id' => $id,
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'email' => $data['email'],
            'telefono' => $data['telefono'],
            'password' => $data['password'],
            'rol_id' => $data['rol_id'],
            'departamento_id' => $data['departamento_id'],
            'estado' => $data['estado'],
            'updated_by' => $data['updated_by'],
            'fecha_contratacion' => $data['fecha_contratacion'],
        ]);
    }

    public function delete(int $id, array $data): bool
    {
        $statement = $this->connection->prepare('UPDATE usuarios SET estado = 0, updated_by = :updated_by WHERE id = :id');
        return $statement->execute(['id' => $id, 'updated_by' => $data['updated_by']]);
    }
}
