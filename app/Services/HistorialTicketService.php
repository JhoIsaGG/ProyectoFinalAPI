<?php

declare(strict_types=1);

/*
| Servicio de historial de tickets.
| Gestiona la lógica de negocio para consultar y guardar el historial de cambios de estado.
*/
class HistorialTicketService
{
    public function __construct(private HistorialTicketRepository $historialTicketRepository)
    {
    }

    public function list(): array
    {
        return $this->historialTicketRepository->getAll();
    }

    public function getByTicketId(int $ticketId): array
    {
        return $this->historialTicketRepository->findByTicketId($ticketId);
    }

    public function create(array $data): array
    {
        $this->validateCreateData($data);
        $newId = $this->historialTicketRepository->create($data);
        return ['id' => $newId] + $data;
    }

    private function validateCreateData(array $data): void
    {
        if (!isset($data['ticket_id']) || !is_numeric($data['ticket_id'])) {
            throw new RuntimeException('El campo ticket_id es obligatorio y debe ser numérico');
        }
        if (!isset($data['estado_ticket_id']) || !is_numeric($data['estado_ticket_id'])) {
            throw new RuntimeException('El campo estado_ticket_id es obligatorio y debe ser numérico');
        }
        if (!isset($data['created_by']) || !is_numeric($data['created_by'])) {
            throw new RuntimeException('El campo created_by es obligatorio y debe ser numérico');
        }
    }
}
