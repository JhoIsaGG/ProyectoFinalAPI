<?php

declare(strict_types=1);

class UsuarioValidator
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

        $email = trim((string) ($data['email'] ?? ''));
        if ($email === '') {
            $errors['email'] = 'El email es obligatorio';
        } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'El email no tiene un formato valido';
        }

        if (empty(trim((string) ($data['telefono'] ?? '')))) {
            $errors['telefono'] = 'El telefono es obligatorio';
        }
        return $errors;
    }
}
