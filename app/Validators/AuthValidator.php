<?php

declare(strict_types=1);

class AuthValidator
{
    public function validateLogin(array $data): array
    {
        $errors = [];

        $email = trim((string) ($data['email'] ?? ''));
        if ($email === '') {
            $errors['email'] = 'El email es obligatorio';
        } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'El email no tiene un formato valido';
        }

        if (($data['password'] ?? '') === '') {
            $errors['password'] = 'La contrasena es obligatoria';
        }

        return $errors;
    }

    public function validateResetPassword(array $data): array
    {
        $errors = $this->validateLogin($data);

        if (!isset($data['updated_by']) || !is_numeric($data['updated_by'])) {
            $errors['updated_by'] = 'El updated_by es obligatorio';
        }

        return $errors;
    }
}

