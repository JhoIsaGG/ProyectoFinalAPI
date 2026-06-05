<?php

declare(strict_types=1);

class EstadoTicketController
{
    public function __construct(
        private EstadoTicketService $estadoTicketService,
        private EstadoTicketValidator $estadoTicketValidator
    ) {
    }

    public function index(Request $request): void
    {
        $ticketStatuses = $this->estadoTicketService->list();

        Response::json([
            'success' => true,
            'message' => 'Lista de estados de ticket obtenida correctamente',
            'data' => $ticketStatuses,
        ]);
    }

    public function show(Request $request): void
    {
        $ticketStatusId = (int) $request->getParam('id');
        $ticketStatus = $this->estadoTicketService->getById($ticketStatusId);

        if ($ticketStatus === false) {
            Response::json([
                'success' => false,
                'message' => 'Estado de ticket no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Estado de ticket obtenido correctamente',
            'data' => $ticketStatus,
        ]);
    }

    public function store(Request $request): void
    {
        $payload = $request->getBody();
        $validationErrors = $this->estadoTicketValidator->validate($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        try {
            $ticketStatus = $this->estadoTicketService->create($payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Estado de ticket creado correctamente',
            'data' => $ticketStatus,
        ], 201);
    }

    public function update(Request $request): void
    {
        $ticketStatusId = (int) $request->getParam('id');
        $payload = $request->getBody();
        $validationErrors = $this->estadoTicketValidator->validateUpdate($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        try {
            $updatedTicketStatus = $this->estadoTicketService->update($ticketStatusId, $payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        if ($updatedTicketStatus === false) {
            Response::json([
                'success' => false,
                'message' => 'Estado de ticket no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Estado de ticket actualizado correctamente',
            'data' => $updatedTicketStatus,
        ]);
    }

    public function destroy(Request $request): void
    {
        $ticketStatusId = (int) $request->getParam('id');
        $payload = $request->getBody();
        $validationErrors = $this->estadoTicketValidator->validateDelete($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        $deleted = $this->estadoTicketService->delete($ticketStatusId, $payload);

        if ($deleted === false) {
            Response::json([
                'success' => false,
                'message' => 'Estado de ticket no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Estado de ticket eliminado correctamente',
        ], 200);
    }
}

