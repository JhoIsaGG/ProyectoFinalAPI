<?php

declare(strict_types=1);


class RolController
{
    public function __construct(
        private RolService $rolService,
        private RolValidator $rolValidator
    ) {
    } 

    public function index(Request $request): void
    {
        $roles = $this->rolService->list();

        Response::json([
            'success' => true,
            'message' => 'Lista de roles obtenida correctamente',
            'data' => $roles,
        ]);
    }

    public function show(Request $request): void
    {
        $roleId = (int) $request->getParam('id');
        $role = $this->rolService->getById($roleId);

        if ($role === false) {
            Response::json([
                'success' => false,
                'message' => 'Rol no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Rol obtenido correctamente',
            'data' => $role,
        ]);
    }

    public function store(Request $request): void
    {
        $payload = $request->getBody();
        $validationErrors = $this->rolValidator->validate($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        try {
            $role = $this->rolService->create($payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Rol creado correctamente',
            'data' => $role,
        ], 201);
    }

    public function update(Request $request): void
    {
        $roleId = (int) $request->getParam('id');
        $payload = $request->getBody();
        $validationErrors = $this->rolValidator->validate($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        try {
            $updatedRole = $this->rolService->update($roleId, $payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        if ($updatedRole === false) {
            Response::json([
                'success' => false,
                'message' => 'Rol no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Rol actualizado correctamente',
            'data' => $updatedRole,
        ]);
    }

    public function destroy(Request $request): void
    {
        $roleId = (int) $request->getParam('id');
        $deleted = $this->rolService->delete($roleId);

        if ($deleted === false) {
            Response::json([
                'success' => false,
                'message' => 'Rol no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Rol eliminado correctamente',
        ], 200);
    }
}
