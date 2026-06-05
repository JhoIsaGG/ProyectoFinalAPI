<?php

declare(strict_types=1);

class AuthController
{
    public function __construct(
        private AuthService $authService,
        private AuthValidator $authValidator
    ) {
    }

    public function login(Request $request): void
    {
        $payload = $request->getBody();
        $validationErrors = $this->authValidator->validateLogin($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        $user = $this->authService->login($payload);

        if ($user === false) {
            Response::json([
                'success' => false,
                'message' => 'Credenciales invalidas',
            ], 401);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Inicio de sesion exitoso',
            'data' => $user,
        ]);
    }

    public function resetPassword(Request $request): void
    {
        $payload = $request->getBody();
        $validationErrors = $this->authValidator->validateResetPassword($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        $user = $this->authService->resetPassword($payload);

        if ($user === false) {
            Response::json([
                'success' => false,
                'message' => 'Usuario no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Contrasena restablecida correctamente',
            'data' => $user,
        ]);
    }
}

