<?php

declare(strict_types=1);

/*
| Controlador de categorias de ticket.
| Recibe las peticiones HTTP y delega validacion y reglas al service.
*/
class CategoriaTicketController
{
    public function __construct(
        private CategoriaTicketService $categoriaTicketService,
        private CategoriaTicketValidator $categoriaTicketValidator
    ) {
    }

    public function index(Request $request): void
    {
        $categories = $this->categoriaTicketService->list();

        Response::json([
            'success' => true,
            'message' => 'Lista de categorias de ticket obtenida correctamente',
            'data' => $categories,
        ]);
    }

    public function show(Request $request): void
    {
        $categoryId = (int) $request->getParam('id');
        $category = $this->categoriaTicketService->getById($categoryId);

        if ($category === false) {
            Response::json([
                'success' => false,
                'message' => 'Categoria de ticket no encontrada',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Categoria de ticket obtenida correctamente',
            'data' => $category,
        ]);
    }

    public function store(Request $request): void
    {
        $payload = $request->getBody();
        $validationErrors = $this->categoriaTicketValidator->validate($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        try {
            $category = $this->categoriaTicketService->create($payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Categoria de ticket creada correctamente',
            'data' => $category,
        ], 201);
    }

    public function update(Request $request): void
    {
        $categoryId = (int) $request->getParam('id');
        $payload = $request->getBody();
        $validationErrors = $this->categoriaTicketValidator->validate($payload, true);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        try {
            $updatedCategory = $this->categoriaTicketService->update($categoryId, $payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        if ($updatedCategory === false) {
            Response::json([
                'success' => false,
                'message' => 'Categoria de ticket no encontrada',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Categoria de ticket actualizada correctamente',
            'data' => $updatedCategory,
        ]);
    }

    public function destroy(Request $request): void
    {
        $categoryId = (int) $request->getParam('id');
        $updatedBy = $this->getOptionalUpdatedBy($request);

        try {
            $deleted = $this->categoriaTicketService->delete($categoryId, $updatedBy);
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
                'message' => 'Categoria de ticket no encontrada',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Categoria de ticket eliminada correctamente',
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
