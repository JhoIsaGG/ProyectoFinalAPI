<?php

declare(strict_types=1);

class EstadoTicketValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty(trim((string) ($data['nombre'] ?? '')))) {
            $errors['nombre'] = 'El nombre es obligatorio';
        }

        if (!isset($data['estado'])) {
            $errors['estado'] = 'El estado es obligatorio';
        }

        if (!isset($data['created_by']) || !is_numeric($data['created_by'])) {
            $errors['created_by'] = 'El created_by es obligatorio';
        }

        if (!isset($data['updated_by']) || !is_numeric($data['updated_by'])) {
            $errors['updated_by'] = 'El updated_by es obligatorio';
        }

        return $errors;
    }

    public function validateUpdate(array $data): array
    {
        $errors = [];

        if (empty(trim((string) ($data['nombre'] ?? '')))) {
            $errors['nombre'] = 'El nombre es obligatorio';
        }

        if (!isset($data['estado'])) {
            $errors['estado'] = 'El estado es obligatorio';
        }

        if (!isset($data['updated_by']) || !is_numeric($data['updated_by'])) {
            $errors['updated_by'] = 'El updated_by es obligatorio';
        }

        return $errors;
    }

    public function validateDelete(array $data): array
    {
        $errors = [];

        if (!isset($data['updated_by']) || !is_numeric($data['updated_by'])) {
            $errors['updated_by'] = 'El updated_by es obligatorio';
        }

        return $errors;
    }
}

