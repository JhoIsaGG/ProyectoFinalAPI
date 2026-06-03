<?php

declare(strict_types=1);

/*
| Validador de comentarios de ticket.
| Verifica que cada comentario pertenezca a un ticket y a un usuario.
*/
class ComentarioTicketValidator
{
    public function validate(array $data, bool $isUpdate = false): array
    {
        // El historial requiere ticket, usuario y descripcion en creacion.
        $errors = [];

        foreach ([
            'ticket_id' => 'El ticket es obligatorio',
            'usuario_id' => 'El usuario es obligatorio',
        ] as $field => $message) {
            if (!$isUpdate || array_key_exists($field, $data)) {
                $this->validateRequiredPositiveInteger($data, $field, $message, $errors);
            }
        }

        if (!$isUpdate || array_key_exists('descripcion', $data)) {
            $descripcion = trim((string) ($data['descripcion'] ?? ''));

            if ($descripcion === '') {
                $errors['descripcion'] = 'La descripcion es obligatoria';
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
