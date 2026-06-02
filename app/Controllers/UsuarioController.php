<?php

declare(strict_types=1);


class UsuarioController
{
    public function __construct(
        private UsuarioService $usuarioService,
        private UsuarioValidator $usuarioValidator
    ) {
    } 

    public function index(Request $request): void
    {
        $users = $this->usuarioService->list();

        Response::json([
            'success' => true,
            'message' => 'Lista de usuarios obtenida correctamente',
            'data' => $users,
        ]);
    }

    public function show(Request $request): void
    {
        $userId = (int) $request->getParam('id');
        $user = $this->usuarioService->getById($userId);

        if ($user === false) {
            Response::json([
                'success' => false,
                'message' => 'Usuario no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Usuario obtenido correctamente',
            'data' => $user,
        ]);
    }

    public function store(Request $request): void
    {
        $payload = $request->getBody();
        $validationErrors = $this->usuarioValidator->validate($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        try {
            $user = $this->usuarioService->create($payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Usuario creado correctamente',
            'data' => $user,
        ], 201);
    }

    public function update(Request $request): void
    {
        $userId = (int) $request->getParam('id');
        $payload = $request->getBody();
        $validationErrors = $this->usuarioValidator->validate($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        try {
            $updatedUser = $this->usuarioService->update($userId, $payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        if ($updatedUser === false) {
            Response::json([
                'success' => false,
                'message' => 'Usuario no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Usuario actualizado correctamente',
            'data' => $updatedUser,
        ]);
    }

    public function destroy(Request $request): void
    {
        $userId = (int) $request->getParam('id');
        $deleted = $this->usuarioService->delete($userId);

        if ($deleted === false) {
            Response::json([
                'success' => false,
                'message' => 'Usuario no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Usuario eliminado correctamente',
        ], 200);
    }
}
