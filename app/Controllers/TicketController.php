<?php

declare(strict_types=1);

/*
| Controlador de tickets.
| Expone el CRUD principal de solicitudes de soporte.
*/
class TicketController
{
    public function __construct(
        private TicketService $ticketService,
        private TicketValidator $ticketValidator
    ) {
    }



    public function index(Request $request): void
    {
        // Los filtros opcionales llegan por query string y se normalizan en el service.
        $tickets = $this->ticketService->list($request->getQueryParams());

        Response::json([
            'success' => true,
            'message' => 'Lista de tickets obtenida correctamente',
            'data' => $tickets,
        ]);
    }

    public function showByUser(Request $request): void
    {
        $userId = (int) $request->getParam('user_id');
        $tickets = $this->ticketService->getByUser($userId);

       if ($tickets === false) {
            Response::json([
                'success' => false,
                'message' => 'Tickets no encontrados',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Lista de tickets obtenida correctamente',
            'data' => $tickets,
        ]);
    }


    public function show(Request $request): void
    {
        $ticketId = (int) $request->getParam('id');
        $ticket = $this->ticketService->getById($ticketId);

        if ($ticket === false) {
            Response::json([
                'success' => false,
                'message' => 'Ticket no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Ticket obtenido correctamente',
            'data' => $ticket,
        ]);
    }

    public function store(Request $request): void
    {
        $payload = $request->getBody();
        $validationErrors = $this->ticketValidator->validate($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        try {
            $ticket = $this->ticketService->create($payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Ticket creado correctamente',
            'data' => $ticket,
        ], 201);
    }

    public function update(Request $request): void
    {
        $ticketId = (int) $request->getParam('id');
        $payload = $request->getBody();
        $validationErrors = $this->ticketValidator->validate($payload, true);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        try {
            $updatedTicket = $this->ticketService->update($ticketId, $payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        if ($updatedTicket === false) {
            Response::json([
                'success' => false,
                'message' => 'Ticket no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Ticket actualizado correctamente',
            'data' => $updatedTicket,
        ]);
    }

    public function destroy(Request $request): void
    {
        $ticketId = (int) $request->getParam('id');
        $updatedBy = $this->getOptionalUpdatedBy($request);

        try {
            $deleted = $this->ticketService->delete($ticketId, $updatedBy);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        if ($deleted === false) {
            Response::json([
                'success' => false,
                'message' => 'Ticket no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Ticket eliminado correctamente',
        ]);
    }

    private function getOptionalUpdatedBy(Request $request): ?int
    {
        // En eliminacion logica updated_by puede llegar por query o body JSON.
        $updatedBy = $request->getParam('updated_by');

        if ($updatedBy === null || $updatedBy === '') {
            return null;
        }

        return (int) $updatedBy;
    }
}
