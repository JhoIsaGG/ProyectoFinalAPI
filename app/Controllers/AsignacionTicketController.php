<?php

declare(strict_types=1);

/*
| Controlador de asignaciones de ticket.
| Expone el CRUD principal de asignaciones en formato JSON REST.
*/
class AsignacionTicketController
{
    public function __construct(
        private AsignacionTicketService $asignacionTicketService,
        private AsignacionTicketValidator $asignacionTicketValidator
    ) {
    }

    public function index(Request $request): void
    {
        $assignments = $this->asignacionTicketService->list();

        Response::json([
            'success' => true,
            'message' => 'Lista de asignaciones obtenida correctamente',
            'data' => $assignments,
        ]);
    }

    public function show(Request $request): void
    {
        $id = (int) $request->getParam('id');
        $assignment = $this->asignacionTicketService->getById($id);

        if ($assignment === false) {
            Response::json([
                'success' => false,
                'message' => 'Asignación no encontrada',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Asignación obtenida correctamente',
            'data' => $assignment,
        ]);
    }

    public function store(Request $request): void
    {
        $payload = $request->getBody();
        $validationErrors = $this->asignacionTicketValidator->validate($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        try {
            $assignment = $this->asignacionTicketService->create($payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Asignación creada correctamente',
            'data' => $assignment,
        ], 201);
    }

    public function update(Request $request): void
    {
        $id = (int) $request->getParam('id');
        $payload = $request->getBody();
        $validationErrors = $this->asignacionTicketValidator->validate($payload, true);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        if (!isset($payload['updated_by']) || (int)$payload['updated_by'] <= 0) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => ['updated_by' => 'El usuario que actualiza (updated_by) es obligatorio'],
            ], 400);
            return;
        }

        try {
            $updated = $this->asignacionTicketService->update($id, $payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        if ($updated === false) {
            Response::json([
                'success' => false,
                'message' => 'Asignación no encontrada',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Asignación actualizada correctamente',
            'data' => $updated,
        ]);
    }

    public function destroy(Request $request): void
    {
        $id = (int) $request->getParam('id');
        $deleted = $this->asignacionTicketService->delete($id);

        if ($deleted === false) {
            Response::json([
                'success' => false,
                'message' => 'Asignación no encontrada',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Asignación eliminada correctamente',
        ]);
    }
}
