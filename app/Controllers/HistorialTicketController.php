<?php

declare(strict_types=1);

/*
| Controlador de historial de tickets.
| Expone los endpoints para visualizar el historial de auditoría de los tickets.
*/
class HistorialTicketController
{
    public function __construct(private HistorialTicketService $historialTicketService)
    {
    }

    public function index(Request $request): void
    {
        $historial = $this->historialTicketService->list();

        Response::json([
            'success' => true,
            'message' => 'Historial de tickets obtenido correctamente',
            'data' => $historial,
        ]);
    }

    public function showByTicket(Request $request): void
    {
        $ticketId = (int) $request->getParam('ticket_id');
        $historial = $this->historialTicketService->getByTicketId($ticketId);

        Response::json([
            'success' => true,
            'message' => sprintf('Historial del ticket %d obtenido correctamente', $ticketId),
            'data' => $historial,
        ]);
    }
}
