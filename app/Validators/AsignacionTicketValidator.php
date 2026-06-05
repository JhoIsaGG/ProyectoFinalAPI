<?php

declare(strict_types=1);

/*
| Validador de asignaciones de ticket.
| Garantiza que el ticket, el agente y los usuarios de auditoría sean válidos.
*/
class AsignacionTicketValidator
{
    public function validate(array $data, bool $isUpdate = false): array
    {
        $errors = [];

        $this->validateRequiredPositiveInteger($data, 'ticket_id', 'El ticket es obligatorio', $errors, $isUpdate);
        $this->validateRequiredPositiveInteger($data, 'agente_id', 'El agente es obligatorio', $errors, $isUpdate);

        if (!$isUpdate) {
            $this->validateRequiredPositiveInteger($data, 'created_by', 'El usuario creador es obligatorio', $errors, false);
        }

        $this->validateOptionalPositiveInteger($data, 'updated_by', $errors);

        return $errors;
    }

    private function validateRequiredPositiveInteger(
        array $data,
        string $field,
        string $message,
        array &$errors,
        bool $isUpdate
    ): void {
        if ($isUpdate && !array_key_exists($field, $data)) {
            return;
        }

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

    private function isPositiveInteger(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false;
    }
}
