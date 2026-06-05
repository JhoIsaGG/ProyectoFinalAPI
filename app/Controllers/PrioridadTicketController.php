<?php

declare(strict_types=1);

class PrioridadTicketController
{
    public function __construct(
        private PrioridadTicketService $prioridadTicketService,
        private PrioridadTicketValidator $prioridadTicketValidator
    ) {
    }

    public function index(Request $request): void
    {
        $ticketPriorities = $this->prioridadTicketService->list();

        Response::json([
            'success' => true,
            'message' => 'Lista de prioridades de ticket obtenida correctamente',
            'data' => $ticketPriorities,
        ]);
    }

    public function show(Request $request): void
    {
        $ticketPriorityId = (int) $request->getParam('id');
        $ticketPriority = $this->prioridadTicketService->getById($ticketPriorityId);

        if ($ticketPriority === false) {
            Response::json([
                'success' => false,
                'message' => 'Prioridad de ticket no encontrada',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Prioridad de ticket obtenida correctamente',
            'data' => $ticketPriority,
        ]);
    }

    public function store(Request $request): void
    {
        $payload = $request->getBody();
        $validationErrors = $this->prioridadTicketValidator->validate($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        try {
            $ticketPriority = $this->prioridadTicketService->create($payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Prioridad de ticket creada correctamente',
            'data' => $ticketPriority,
        ], 201);
    }

    public function update(Request $request): void
    {
        $ticketPriorityId = (int) $request->getParam('id');
        $payload = $request->getBody();
        $validationErrors = $this->prioridadTicketValidator->validateUpdate($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        try {
            $updatedTicketPriority = $this->prioridadTicketService->update($ticketPriorityId, $payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        if ($updatedTicketPriority === false) {
            Response::json([
                'success' => false,
                'message' => 'Prioridad de ticket no encontrada',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Prioridad de ticket actualizada correctamente',
            'data' => $updatedTicketPriority,
        ]);
    }

    public function destroy(Request $request): void
    {
        $ticketPriorityId = (int) $request->getParam('id');
        $payload = $request->getBody();
        $validationErrors = $this->prioridadTicketValidator->validateDelete($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        $deleted = $this->prioridadTicketService->delete($ticketPriorityId, $payload);

        if ($deleted === false) {
            Response::json([
                'success' => false,
                'message' => 'Prioridad de ticket no encontrada',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Prioridad de ticket eliminada correctamente',
        ], 200);
    }
}

