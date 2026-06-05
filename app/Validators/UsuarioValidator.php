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

        $rolId = $data['rol_id'] ?? null;
        if ($rolId === null || $rolId === '') {
            $errors['rol_id'] = 'El rol_id es obligatorio';
        } elseif (filter_var($rolId, FILTER_VALIDATE_INT) === false || (int)$rolId <= 0) {
            $errors['rol_id'] = 'El rol_id debe ser un entero valido';
        }

        $deptoId = $data['departamento_id'] ?? null;
        if ($deptoId === null || $deptoId === '') {
            $errors['departamento_id'] = 'El departamento_id es obligatorio';
        } elseif (filter_var($deptoId, FILTER_VALIDATE_INT) === false || (int)$deptoId <= 0) {
            $errors['departamento_id'] = 'El departamento_id debe ser un entero valido';
        }

        if (isset($errors['rol_id']) === false && (int)$rolId === 2) {
            $categories = $data['categoria_ticket_id'] ?? null;
            if ($categories === null) {
                $errors['categoria_ticket_id'] = 'El agente requiere al menos una categoria asignada (categoria_ticket_id)';
            } elseif (is_array($categories) === false) {
                $errors['categoria_ticket_id'] = 'Las categorias deben ser enviadas como una lista (array)';
            } elseif (count($categories) === 0) {
                $errors['categoria_ticket_id'] = 'El agente requiere al menos una categoria asignada (categoria_ticket_id)';
            } else {
                foreach ($categories as $catId) {
                    if (filter_var($catId, FILTER_VALIDATE_INT) === false || (int)$catId <= 0) {
                        $errors['categoria_ticket_id'] = 'Todas las categorias deben ser identificadores enteros validos';
                        break;
                    }
                }
            }
        }

        return $errors;
    }
}
