<?php

declare(strict_types=1);

class DepartamentoValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty(trim((string) ($data['nombre'] ?? '')))) {
            $errors['nombre'] = 'El nombre es obligatorio';
        }

        if (empty(trim((string) ($data['descripcion'] ?? '')))) {
            $errors['descripcion'] = 'La descripción es obligatoria';
        }

        return $errors;
    }
}
