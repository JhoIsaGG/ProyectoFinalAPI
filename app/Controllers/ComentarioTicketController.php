<?php

declare(strict_types=1);

/*
| Controlador de comentarios de ticket.
| Maneja el historial de atencion asociado a cada solicitud.
*/
class ComentarioTicketController
{
    public function __construct(
        private ComentarioTicketService $comentarioTicketService,
        private ComentarioTicketValidator $comentarioTicketValidator
    ) {
    }

    public function index(Request $request): void
    {
        $filters = $request->getQueryParams();
        $routeTicketId = $request->getParam('ticket_id');

        if ($routeTicketId !== null) {
            // Permite consultar el historial usando /tickets/{ticket_id}/comentarios.
            $filters['ticket_id'] = $routeTicketId;
        }

        $comments = $this->comentarioTicketService->list($filters);

        Response::json([
            'success' => true,
            'message' => 'Lista de comentarios de ticket obtenida correctamente',
            'data' => $comments,
        ]);
    }

    public function show(Request $request): void
    {
        $commentId = (int) $request->getParam('id');
        $comment = $this->comentarioTicketService->getById($commentId);

        if ($comment === false) {
            Response::json([
                'success' => false,
                'message' => 'Comentario de ticket no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Comentario de ticket obtenido correctamente',
            'data' => $comment,
        ]);
    }

    public function store(Request $request): void
    {
        $payload = $request->getBody();
        $routeTicketId = $request->getParam('ticket_id');

        if ($routeTicketId !== null) {
            // Si el ticket viene en la ruta, se usa como fuente principal.
            $payload['ticket_id'] = $routeTicketId;
        }

        $validationErrors = $this->comentarioTicketValidator->validate($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        try {
            $comment = $this->comentarioTicketService->create($payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Comentario de ticket creado correctamente',
            'data' => $comment,
        ], 201);
    }

    public function update(Request $request): void
    {
        $commentId = (int) $request->getParam('id');
        $payload = $request->getBody();
        $validationErrors = $this->comentarioTicketValidator->validate($payload, true);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        try {
            $updatedComment = $this->comentarioTicketService->update($commentId, $payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        if ($updatedComment === false) {
            Response::json([
                'success' => false,
                'message' => 'Comentario de ticket no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Comentario de ticket actualizado correctamente',
            'data' => $updatedComment,
        ]);
    }

    public function destroy(Request $request): void
    {
        $commentId = (int) $request->getParam('id');
        $updatedBy = $this->getOptionalUpdatedBy($request);

        try {
            $deleted = $this->comentarioTicketService->delete($commentId, $updatedBy);
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
                'message' => 'Comentario de ticket no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Comentario de ticket eliminado correctamente',
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
