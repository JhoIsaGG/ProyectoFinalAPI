<?php 

declare(strict_types=1);
// Rutas para Usuarios
$router->get('/proyectofinalapi/public/api/usuarios', [$usuarioController, 'index'], $protectedMiddlewares);
$router->get('/proyectofinalapi/public/api/usuarios/{id}', [$usuarioController, 'show'], $protectedMiddlewares);
$router->post('/proyectofinalapi/public/api/usuarios', [$usuarioController, 'store'], $protectedMiddlewares);
$router->put('/proyectofinalapi/public/api/usuarios/{id}', [$usuarioController, 'update'], $protectedMiddlewares);
$router->delete('/proyectofinalapi/public/api/usuarios/{id}', [$usuarioController, 'destroy'], $protectedMiddlewares);

// Rutas para Roles
$router->get('/proyectofinalapi/public/api/roles', [$rolController, 'index'], $protectedMiddlewares);
$router->get('/proyectofinalapi/public/api/roles/{id}', [$rolController, 'show'], $protectedMiddlewares);
$router->post('/proyectofinalapi/public/api/roles', [$rolController, 'store'], $protectedMiddlewares);
$router->put('/proyectofinalapi/public/api/roles/{id}', [$rolController, 'update'], $protectedMiddlewares);
$router->delete('/proyectofinalapi/public/api/roles/{id}', [$rolController, 'destroy'], $protectedMiddlewares);

// Rutas para Departamentos
$router->get('/proyectofinalapi/public/api/departamentos', [$departamentoController, 'index'], $protectedMiddlewares);
$router->get('/proyectofinalapi/public/api/departamentos/{id}', [$departamentoController, 'show'], $protectedMiddlewares);
$router->post('/proyectofinalapi/public/api/departamentos', [$departamentoController, 'store'], $protectedMiddlewares);
$router->put('/proyectofinalapi/public/api/departamentos/{id}', [$departamentoController, 'update'], $protectedMiddlewares);
$router->delete('/proyectofinalapi/public/api/departamentos/{id}', [$departamentoController, 'destroy'], $protectedMiddlewares);

// Auth (publico)
$router->post('/proyectofinalapi/public/api/auth/login', [$authController, 'login']);
$router->post('/proyectofinalapi/public/api/auth/restablecer-contrasena', [$authController, 'resetPassword']);

// Estados de ticket
$router->get('/proyectofinalapi/public/api/estados-ticket', [$estadoTicketController, 'index'], $protectedMiddlewares);
$router->get('/proyectofinalapi/public/api/estados-ticket/{id}', [$estadoTicketController, 'show'], $protectedMiddlewares);
$router->post('/proyectofinalapi/public/api/estados-ticket', [$estadoTicketController, 'store'], $protectedMiddlewares);
$router->put('/proyectofinalapi/public/api/estados-ticket/{id}', [$estadoTicketController, 'update'], $protectedMiddlewares);
$router->delete('/proyectofinalapi/public/api/estados-ticket/{id}', [$estadoTicketController, 'destroy'], $protectedMiddlewares);

// Prioridades de ticket
$router->get('/proyectofinalapi/public/api/prioridades-ticket', [$prioridadTicketController, 'index'], $protectedMiddlewares);
$router->get('/proyectofinalapi/public/api/prioridades-ticket/{id}', [$prioridadTicketController, 'show'], $protectedMiddlewares);
$router->post('/proyectofinalapi/public/api/prioridades-ticket', [$prioridadTicketController, 'store'], $protectedMiddlewares);
$router->put('/proyectofinalapi/public/api/prioridades-ticket/{id}', [$prioridadTicketController, 'update'], $protectedMiddlewares);
$router->delete('/proyectofinalapi/public/api/prioridades-ticket/{id}', [$prioridadTicketController, 'destroy'], $protectedMiddlewares);

// Rutas para Categorias de Ticket
    $router->get('/proyectofinalapi/public/api/categorias_ticket', [$categoriaTicketController, 'index'], $protectedMiddlewares);
    $router->get('/proyectofinalapi/public/api/categorias_ticket/{id}', [$categoriaTicketController, 'show'], $protectedMiddlewares);
    $router->post('/proyectofinalapi/public/api/categorias_ticket', [$categoriaTicketController, 'store'], $protectedMiddlewares);
    $router->put('/proyectofinalapi/public/api/categorias_ticket/{id}', [$categoriaTicketController, 'update'], $protectedMiddlewares);
    $router->delete('/proyectofinalapi/public/api/categorias_ticket/{id}', [$categoriaTicketController, 'destroy'], $protectedMiddlewares);

// Rutas para Tickets
    $router->get('/proyectofinalapi/public/api/tickets', [$ticketController, 'index'], $protectedMiddlewares);
    $router->get('/proyectofinalapi/public/api/tickets/{id}', [$ticketController, 'show'], $protectedMiddlewares);
    $router->post('/proyectofinalapi/public/api/tickets', [$ticketController, 'store'], $protectedMiddlewares);
    $router->put('/proyectofinalapi/public/api/tickets/{id}', [$ticketController, 'update'], $protectedMiddlewares);
    $router->delete('/proyectofinalapi/public/api/tickets/{id}', [$ticketController, 'destroy'], $protectedMiddlewares);


// Rutas para Comentarios de Ticket
    $router->get('/proyectofinalapi/public/api/comentarios_ticket', [$comentarioTicketController, 'index'], $protectedMiddlewares);
    $router->get('/proyectofinalapi/public/api/comentarios_ticket/{id}', [$comentarioTicketController, 'show'], $protectedMiddlewares);
    $router->post('/proyectofinalapi/public/api/comentarios_ticket', [$comentarioTicketController, 'store'], $protectedMiddlewares);
    $router->put('/proyectofinalapi/public/api/comentarios_ticket/{id}', [$comentarioTicketController, 'update'], $protectedMiddlewares);
    $router->delete('/proyectofinalapi/public/api/comentarios_ticket/{id}', [$comentarioTicketController, 'destroy'], $protectedMiddlewares);
    $router->get('/proyectofinalapi/public/api/tickets/{ticket_id}/comentarios', [$comentarioTicketController, 'index'], $protectedMiddlewares);
    $router->post('/proyectofinalapi/public/api/tickets/{ticket_id}/comentarios', [$comentarioTicketController, 'store'], $protectedMiddlewares);

// Rutas para Historial de Tickets
    $router->get('/proyectofinalapi/public/api/tickets/{ticket_id}/historial', [$historialTicketController, 'showByTicket'], $protectedMiddlewares);
    $router->get('/proyectofinalapi/public/api/historial-tickets', [$historialTicketController, 'index'], $protectedMiddlewares);

// Rutas para Asignaciones de Tickets
    $router->get('/proyectofinalapi/public/api/asignaciones', [$asignacionTicketController, 'index'], $protectedMiddlewares);
    $router->get('/proyectofinalapi/public/api/asignaciones/{id}', [$asignacionTicketController, 'show'], $protectedMiddlewares);
    $router->post('/proyectofinalapi/public/api/asignaciones', [$asignacionTicketController, 'store'], $protectedMiddlewares);
    $router->put('/proyectofinalapi/public/api/asignaciones/{id}', [$asignacionTicketController, 'update'], $protectedMiddlewares);
    $router->delete('/proyectofinalapi/public/api/asignaciones/{id}', [$asignacionTicketController, 'destroy'], $protectedMiddlewares);

?>