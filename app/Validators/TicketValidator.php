<?php

declare(strict_types=1);

/*
| Validador de tickets.
| Garantiza que la solicitud tenga titulo, descripcion y relaciones validas.
*/
class TicketValidator
{
    public function validate(array $data, bool $isUpdate = false): array
    {
        // En update solo se valida lo que venga en el payload.
        $errors = [];

        $this->validateStringField($data, 'titulo', 'El titulo es obligatorio', 100, $errors, $isUpdate);
        $this->validateStringField($data, 'descripcion', 'La descripcion es obligatoria', null, $errors, $isUpdate);
        $this->validateTicketRelations($data, $errors, $isUpdate);

        if (!$isUpdate) {
            $this->validateRequiredPositiveInteger($data, 'created_by', 'El usuario creador es obligatorio', $errors);
        }

        $this->validateOptionalPositiveInteger($data, 'updated_by', $errors);
        $this->validateOptionalEstado($data, $errors);

        return $errors;
    }

    private function validateTicketRelations(array $data, array &$errors, bool $isUpdate): void
    {
        // Estas llaves foraneas conectan el ticket con estado, prioridad y categoria.
        $relations = [
            'estado_ticket_id' => 'El estado del ticket es obligatorio',
            'prioridad_ticket_id' => 'La prioridad del ticket es obligatoria',
            'categoria_ticket_id' => 'La categoria del ticket es obligatoria',
        ];

        foreach ($relations as $field => $message) {
            if (!$isUpdate || array_key_exists($field, $data)) {
                $this->validateRequiredPositiveInteger($data, $field, $message, $errors);
            }
        }
    }

    private function validateStringField(
        array $data,
        string $field,
        string $message,
        ?int $maxLength,
        array &$errors,
        bool $isUpdate
    ): void {
        if ($isUpdate && !array_key_exists($field, $data)) {
            return;
        }

        $value = trim((string) ($data[$field] ?? ''));

        if ($value === '') {
            $errors[$field] = $message;
            return;
        }

        if ($maxLength !== null && strlen($value) > $maxLength) {
            $errors[$field] = sprintf('No debe superar %d caracteres', $maxLength);
        }
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
