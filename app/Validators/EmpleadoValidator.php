<?php

declare(strict_types=1);

class EmpleadoValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty(trim((string) ($data['nombre'] ?? '')))) {
            $errors['nombre'] = 'El nombre es obligatorio';
        }

        if (empty(trim((string) ($data['apellido'] ?? '')))) {
            $errors['apellido'] = 'El apellido es obligatorio';
        }

        $correo = trim((string) ($data['correo'] ?? ''));
        if ($correo === '') {
            $errors['correo'] = 'El correo es obligatorio';
        } elseif (filter_var($correo, FILTER_VALIDATE_EMAIL) === false) {
            $errors['correo'] = 'El correo no tiene un formato valido';
        }

        if (empty(trim((string) ($data['puesto'] ?? '')))) {
            $errors['puesto'] = 'El puesto es obligatorio';
        }

        $salario = $data['salario'] ?? null;
        if (!is_numeric($salario) || (float) $salario <= 0) {
            $errors['salario'] = 'El salario debe ser mayor que 0';
        }

        if (empty(trim((string) ($data['fecha_contratacion'] ?? '')))) {
            $errors['fecha_contratacion'] = 'La fecha de contratacion es obligatoria';
        }

        return $errors;
    }
}
