<?php

declare(strict_types=1);

/*
| Validador de categorias de ticket.
| Revisa datos de entrada antes de enviarlos al service.
*/
class CategoriaTicketValidator
{
    public function validate(array $data, bool $isUpdate = false): array
    {
        // En creacion se requieren campos base; en update se permiten cambios parciales.
        $errors = [];

        if (!$isUpdate || array_key_exists('nombre', $data)) {
            $nombre = trim((string) ($data['nombre'] ?? ''));

            if ($nombre === '') {
                $errors['nombre'] = 'El nombre es obligatorio';
            } elseif (strlen($nombre) > 50) {
                $errors['nombre'] = 'El nombre no debe superar 50 caracteres';
            }
        }

        if (!$isUpdate) {
            $this->validateRequiredPositiveInteger($data, 'created_by', 'El usuario creador es obligatorio', $errors);
        }

        $this->validateOptionalPositiveInteger($data, 'updated_by', $errors);
        $this->validateOptionalEstado($data, $errors);

        return $errors;
    }

    private function validateRequiredPositiveInteger(array $data, string $field, string $message, array &$errors): void
    {
        if (!isset($data[$field]) || !$this->isPositiveInteger($data[$field])) {
            $errors[$field] = $message;
        }
    }

    private function validateOptionalPositiveInteger(array $data, string $field, array &$errors): void
    {
        if (!array_key_exists($field, $data) || $data[$field] === null || $data[$field] === '') {
            return;
        }

        if (!$this->isPositiveInteger($data[$field])) {
            $errors[$field] = 'Debe ser un entero positivo';
        }
    }

    private function validateOptionalEstado(array $data, array &$errors): void
    {
        if (!array_key_exists('estado', $data)) {
            return;
        }

        if (!in_array($data['estado'], [0, 1, '0', '1', true, false], true)) {
            $errors['estado'] = 'El estado debe ser 0 o 1';
        }
    }

    private function isPositiveInteger(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false;
    }
}
