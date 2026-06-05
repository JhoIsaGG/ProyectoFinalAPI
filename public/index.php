<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/config/env.php';
loadEnv(BASE_PATH . '/.env');

spl_autoload_register(function (string $classname) : void {
    $directories = [
        BASE_PATH . '/core/',
        BASE_PATH . '/app/Controllers/',
        BASE_PATH . '/app/Services/',
        BASE_PATH . '/app/Repositories/',
        BASE_PATH . '/app/Middlewares/',
        BASE_PATH . '/app/Validators/',
    ];

    foreach($directories as $directory) {
        $file = $directory . $classname . '.php';

        if(file_exists($file)){
            require $file;
            return;
        }
    }
});


$request = new Request();
$router = new Router();

$databaseConnection = Database::getConnection();
// Repositorios, Servicios, Validadores y Controladores para Usuarios
$usuarioRepository = new UsuarioRepository($databaseConnection);
$usuarioService = new UsuarioService($usuarioRepository);
$usuarioValidator = new UsuarioValidator();
$usuarioController = new UsuarioController($usuarioService, $usuarioValidator);

// Repositorios, Servicios, Validadores y Controladores para Roles
$rolRepository = new RolRepository($databaseConnection);
$rolService = new RolService($rolRepository);
$rolValidator = new RolValidator();
$rolController = new RolController($rolService, $rolValidator);

// Repositorios, Servicios, Validadores y Controladores para Departamentos
$departamentoRepository = new DepartamentoRepository($databaseConnection);
$departamentoService = new DepartamentoService($departamentoRepository);
$departamentoValidator = new DepartamentoValidator();
$departamentoController = new DepartamentoController($departamentoService, $departamentoValidator);

// Auth (reutiliza UsuarioRepository)
$authService = new AuthService($usuarioRepository);
$authValidator = new AuthValidator();
$authController = new AuthController($authService, $authValidator);

// Estados de ticket
$estadoTicketRepository = new EstadoTicketRepository($databaseConnection);
$estadoTicketService = new EstadoTicketService($estadoTicketRepository);
$estadoTicketValidator = new EstadoTicketValidator();
$estadoTicketController = new EstadoTicketController($estadoTicketService, $estadoTicketValidator);

// Prioridades de ticket
$prioridadTicketRepository = new PrioridadTicketRepository($databaseConnection);
$prioridadTicketService = new PrioridadTicketService($prioridadTicketRepository);
$prioridadTicketValidator = new PrioridadTicketValidator();
$prioridadTicketController = new PrioridadTicketController($prioridadTicketService, $prioridadTicketValidator);
// Repositorios, Servicios, Validadores y Controladores para Categorias de Ticket
$categoriaTicketRepository = new CategoriaTicketRepository($databaseConnection);
$categoriaTicketService = new CategoriaTicketService($categoriaTicketRepository);
$categoriaTicketValidator = new CategoriaTicketValidator();
$categoriaTicketController = new CategoriaTicketController($categoriaTicketService, $categoriaTicketValidator);

// Repositorios, Servicios, Validadores y Controladores para Tickets
$historialTicketRepository = new HistorialTicketRepository($databaseConnection);
$historialTicketService = new HistorialTicketService($historialTicketRepository);
$historialTicketController = new HistorialTicketController($historialTicketService);

$ticketRepository = new TicketRepository($databaseConnection);
$ticketService = new TicketService($ticketRepository, $historialTicketRepository);
$ticketValidator = new TicketValidator();
$ticketController = new TicketController($ticketService, $ticketValidator);

// Repositorios, Servicios, Validadores y Controladores para Comentarios de Ticket
$comentarioTicketRepository = new ComentarioTicketRepository($databaseConnection);
$comentarioTicketService = new ComentarioTicketService($comentarioTicketRepository);
$comentarioTicketValidator = new ComentarioTicketValidator();
$comentarioTicketController = new ComentarioTicketController($comentarioTicketService, $comentarioTicketValidator);

// Repositorios, Servicios, Validadores y Controladores para Asignaciones de Ticket
$asignacionTicketRepository = new AsignacionTicketRepository($databaseConnection);
$asignacionTicketService = new AsignacionTicketService($asignacionTicketRepository);
$asignacionTicketValidator = new AsignacionTicketValidator();
$asignacionTicketController = new AsignacionTicketController($asignacionTicketService, $asignacionTicketValidator);


$corsMiddleware = new CorsMiddleware();
$jsonMiddleware = new JsonMiddleware();
$authMiddleware = new AuthMiddleware();

$corsMiddleware->handle($request);
$jsonMiddleware->handle($request);

$protectedMiddlewares = [$authMiddleware];

require BASE_PATH . '/routes/api.php';

$router->dispatch($request);

?>