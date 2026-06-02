<?php

declare(strict_types=1);


class EmpleadoController
{
    public function __construct(
        private EmpleadoService $empleadoService,
        private EmpleadoValidator $empleadoValidator
    ) {
    } 

    public function index(Request $request): void
    {
        $employees = $this->empleadoService->list();

        Response::json([
            'success' => true,
            'message' => 'Lista de empleados obtenida correctamente',
            'data' => $employees,
        ]);
    }

    public function show(Request $request): void
    {
        $employeeId = (int) $request->getParam('id');
        $employee = $this->empleadoService->getById($employeeId);

        if ($employee === false) {
            Response::json([
                'success' => false,
                'message' => 'Empleado no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Empleado obtenido correctamente',
            'data' => $employee,
        ]);
    }

    public function store(Request $request): void
    {
        $payload = $request->getBody();
        $validationErrors = $this->empleadoValidator->validate($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        try {
            $employee = $this->empleadoService->create($payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Empleado creado correctamente',
            'data' => $employee,
        ], 201);
    }

    public function update(Request $request): void
    {
        $employeeId = (int) $request->getParam('id');
        $payload = $request->getBody();
        $validationErrors = $this->empleadoValidator->validate($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        try {
            $updatedEmployee = $this->empleadoService->update($employeeId, $payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        if ($updatedEmployee === false) {
            Response::json([
                'success' => false,
                'message' => 'Empleado no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Empleado actualizado correctamente',
            'data' => $updatedEmployee,
        ]);
    }

    public function destroy(Request $request): void
    {
        $employeeId = (int) $request->getParam('id');
        $deleted = $this->empleadoService->delete($employeeId);

        if ($deleted === false) {
            Response::json([
                'success' => false,
                'message' => 'Empleado no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Empleado eliminado correctamente',
        ], 200);
    }
}
