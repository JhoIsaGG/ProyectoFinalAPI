<?php 

declare(strict_types=1);
// Rutas para Usuarios
$router->get('/api/usuarios', [$usuarioController, 'index'], $protectedMiddlewares);
$router->get('/api/usuarios/{id}', [$usuarioController, 'show'], $protectedMiddlewares);
$router->post('/api/usuarios', [$usuarioController, 'store'], $protectedMiddlewares);
$router->put('/api/usuarios/{id}', [$usuarioController, 'update'], $protectedMiddlewares);
$router->delete('/api/usuarios/{id}', [$usuarioController, 'destroy'], $protectedMiddlewares);

// Rutas para Roles
$router->get('/api/roles', [$rolController, 'index'], $protectedMiddlewares);
$router->get('/api/roles/{id}', [$rolController, 'show'], $protectedMiddlewares);
$router->post('/api/roles', [$rolController, 'store'], $protectedMiddlewares);
$router->put('/api/roles/{id}', [$rolController, 'update'], $protectedMiddlewares);
$router->delete('/api/roles/{id}', [$rolController, 'destroy'], $protectedMiddlewares);

// Rutas para Departamentos
$router->get('/api/departamentos', [$departamentoController, 'index'], $protectedMiddlewares);
$router->get('/api/departamentos/{id}', [$departamentoController, 'show'], $protectedMiddlewares);
$router->post('/api/departamentos', [$departamentoController, 'store'], $protectedMiddlewares);
$router->put('/api/departamentos/{id}', [$departamentoController, 'update'], $protectedMiddlewares);
$router->delete('/api/departamentos/{id}', [$departamentoController, 'destroy'], $protectedMiddlewares);

// Auth (publico)
$router->post('/api/auth/login', [$authController, 'login']);
$router->post('/api/auth/restablecer-contrasena', [$authController, 'resetPassword']);

// Estados de ticket
$router->get('/api/estados-ticket', [$estadoTicketController, 'index'], $protectedMiddlewares);
$router->get('/api/estados-ticket/{id}', [$estadoTicketController, 'show'], $protectedMiddlewares);
$router->post('/api/estados-ticket', [$estadoTicketController, 'store'], $protectedMiddlewares);
$router->put('/api/estados-ticket/{id}', [$estadoTicketController, 'update'], $protectedMiddlewares);
$router->delete('/api/estados-ticket/{id}', [$estadoTicketController, 'destroy'], $protectedMiddlewares);

// Prioridades de ticket
$router->get('/api/prioridades-ticket', [$prioridadTicketController, 'index'], $protectedMiddlewares);
$router->get('/api/prioridades-ticket/{id}', [$prioridadTicketController, 'show'], $protectedMiddlewares);
$router->post('/api/prioridades-ticket', [$prioridadTicketController, 'store'], $protectedMiddlewares);
$router->put('/api/prioridades-ticket/{id}', [$prioridadTicketController, 'update'], $protectedMiddlewares);
$router->delete('/api/prioridades-ticket/{id}', [$prioridadTicketController, 'destroy'], $protectedMiddlewares);

// Rutas para Categorias de Ticket
    $router->get('/api/categorias_ticket', [$categoriaTicketController, 'index'], $protectedMiddlewares);
    $router->get('/api/categorias_ticket/{id}', [$categoriaTicketController, 'show'], $protectedMiddlewares);
    $router->post('/api/categorias_ticket', [$categoriaTicketController, 'store'], $protectedMiddlewares);
    $router->put('/api/categorias_ticket/{id}', [$categoriaTicketController, 'update'], $protectedMiddlewares);
    $router->delete('/api/categorias_ticket/{id}', [$categoriaTicketController, 'destroy'], $protectedMiddlewares);

// Rutas para Tickets
    $router->get('/api/tickets_user/{user_id}', [$ticketController, 'showByUser'], $protectedMiddlewares);
    $router->get('/api/tickets', [$ticketController, 'index'], $protectedMiddlewares);
    $router->get('/api/tickets/{id}', [$ticketController, 'show'], $protectedMiddlewares);
    $router->post('/api/tickets', [$ticketController, 'store'], $protectedMiddlewares);
    $router->put('/api/tickets/{id}', [$ticketController, 'update'], $protectedMiddlewares);
    $router->delete('/api/tickets/{id}', [$ticketController, 'destroy'], $protectedMiddlewares);


// Rutas para Comentarios de Ticket
    $router->get('/api/comentarios_ticket', [$comentarioTicketController, 'index'], $protectedMiddlewares);
    $router->get('/api/comentarios_ticket/{id}', [$comentarioTicketController, 'show'], $protectedMiddlewares);
    $router->post('/api/comentarios_ticket', [$comentarioTicketController, 'store'], $protectedMiddlewares);
    $router->put('/api/comentarios_ticket/{id}', [$comentarioTicketController, 'update'], $protectedMiddlewares);
    $router->delete('/api/comentarios_ticket/{id}', [$comentarioTicketController, 'destroy'], $protectedMiddlewares);
    $router->get('/api/tickets/{ticket_id}/comentarios', [$comentarioTicketController, 'index'], $protectedMiddlewares);
    $router->post('/api/tickets/{ticket_id}/comentarios', [$comentarioTicketController, 'store'], $protectedMiddlewares);

// Rutas para Historial de Tickets
    $router->get('/api/tickets/{ticket_id}/historial', [$historialTicketController, 'showByTicket'], $protectedMiddlewares);
    $router->get('/api/historial-tickets', [$historialTicketController, 'index'], $protectedMiddlewares);

// Rutas para Asignaciones de Tickets
    $router->get('/api/asignaciones_user/{user_id}', [$asignacionTicketController, 'showByUser'], $protectedMiddlewares);
    $router->get('/api/asignaciones', [$asignacionTicketController, 'index'], $protectedMiddlewares);
    $router->get('/api/asignaciones/{id}', [$asignacionTicketController, 'show'], $protectedMiddlewares);
    $router->post('/api/asignaciones', [$asignacionTicketController, 'store'], $protectedMiddlewares);
    $router->put('/api/asignaciones/{id}', [$asignacionTicketController, 'update'], $protectedMiddlewares);
    $router->delete('/api/asignaciones/{id}', [$asignacionTicketController, 'destroy'], $protectedMiddlewares);

?>