<?php

declare(strict_types=1);

class EmpleadoRepository
{
    public function __construct(private PDO $connection)
    {
    }

    public function getAll(): array
    {
        $statement = $this->connection->query('SELECT * FROM empleados ORDER BY id DESC');
        return $statement->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $statement = $this->connection->prepare('SELECT * FROM empleados WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->fetch();
    }

    public function create(array $data): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO empleados (nombre, apellido, correo, puesto, salario, fecha_contratacion)
             VALUES (:nombre, :apellido, :correo, :puesto, :salario, :fecha_contratacion)'
        );

        $statement->execute([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'correo' => $data['correo'],
            'puesto' => $data['puesto'],
            'salario' => $data['salario'],
            'fecha_contratacion' => $data['fecha_contratacion'],
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $statement = $this->connection->prepare(
            'UPDATE empleados
             SET nombre = :nombre,
                 apellido = :apellido,
                 correo = :correo,
                 puesto = :puesto,
                 salario = :salario,
                 fecha_contratacion = :fecha_contratacion
             WHERE id = :id'
        );

        return $statement->execute([
            'id' => $id,
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'correo' => $data['correo'],
            'puesto' => $data['puesto'],
            'salario' => $data['salario'],
            'fecha_contratacion' => $data['fecha_contratacion'],
        ]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->connection->prepare('DELETE FROM empleados WHERE id = :id');
        return $statement->execute(['id' => $id]);
    }
}
