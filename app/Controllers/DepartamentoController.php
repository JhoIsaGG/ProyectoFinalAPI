<?php

declare(strict_types=1);


class DepartamentoController
{
    public function __construct(
        private DepartamentoService $departamentoService,
        private DepartamentoValidator $departamentoValidator
    ) {
    } 

    public function index(Request $request): void
    {
        $departments = $this->departamentoService->list();

        Response::json([
            'success' => true,
            'message' => 'Lista de departamentos obtenida correctamente',
            'data' => $departments,
        ]);
    }

    public function show(Request $request): void
    {
        $departmentId = (int) $request->getParam('id');
        $department = $this->departamentoService->getById($departmentId);

        if ($department === false) {
            Response::json([
                'success' => false,
                'message' => 'Departamento no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Departamento obtenido correctamente',
            'data' => $department,
        ]);
    }

    public function store(Request $request): void
    {
        $payload = $request->getBody();
        $validationErrors = $this->departamentoValidator->validate($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        try {
            $department = $this->departamentoService->create($payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Departamento creado correctamente',
            'data' => $department,
        ], 201);
    }

    public function update(Request $request): void
    {
        $departmentId = (int) $request->getParam('id');
        $payload = $request->getBody();
        $validationErrors = $this->departamentoValidator->validate($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        try {
            $updatedDepartment = $this->departamentoService->update($departmentId, $payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        if ($updatedDepartment === false) {
            Response::json([
                'success' => false,
                'message' => 'Departamento no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Departamento actualizado correctamente',
            'data' => $updatedDepartment,
        ]);
    }

    public function destroy(Request $request): void
    {
        $departmentId = (int) $request->getParam('id');
        $deleted = $this->departamentoService->delete($departmentId);

        if ($deleted === false) {
            Response::json([
                'success' => false,
                'message' => 'Departamento no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Departamento eliminado correctamente',
        ], 200);
    }
}
