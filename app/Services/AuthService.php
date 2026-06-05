<?php

declare(strict_types=1);

class AuthService
{
    public function __construct(private UsuarioRepository $usuarioRepository)
    {
    }

    public function login(array $data): array|false
    {
        $email = trim((string) $data['email']);
        $password = (string) $data['password'];
        $user = $this->usuarioRepository->findByEmail($email);

        if ($user === false || (int) $user['estado'] !== 1) {
            return false;
        }

        if (!password_verify($password, (string) $user['password'])) {
            return false;
        }

        unset($user['password']);

        return $user;
    }

    public function resetPassword(array $data): array|false
    {
        $email = trim((string) $data['email']);
        $password = (string) $data['password'];
        $updatedBy = (int) $data['updated_by'];
        $user = $this->usuarioRepository->findByEmail($email);

        if ($user === false || (int) $user['estado'] !== 1) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updated = $this->usuarioRepository->updatePassword((int) $user['id'], $hashedPassword, $updatedBy);

        if (!$updated) {
            return false;
        }

        $updatedUser = $this->usuarioRepository->findById((int) $user['id']);

        if ($updatedUser === false) {
            return false;
        }

        unset($updatedUser['password']);

        return $updatedUser;
    }
}
