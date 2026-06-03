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
// Repositorios, Servicios, Validadores y Controladores para Empleados
$empleadoRepository = new EmpleadoRepository($databaseConnection);
$empleadoService = new EmpleadoService($empleadoRepository);
$empleadoValidator = new EmpleadoValidator();
$empleadoController = new EmpleadoController($empleadoService, $empleadoValidator);

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

// Repositorios, Servicios, Validadores y Controladores para Categorias de Ticket
$categoriaTicketRepository = new CategoriaTicketRepository($databaseConnection);
$categoriaTicketService = new CategoriaTicketService($categoriaTicketRepository);
$categoriaTicketValidator = new CategoriaTicketValidator();
$categoriaTicketController = new CategoriaTicketController($categoriaTicketService, $categoriaTicketValidator);

// Repositorios, Servicios, Validadores y Controladores para Tickets
$ticketRepository = new TicketRepository($databaseConnection);
$ticketService = new TicketService($ticketRepository);
$ticketValidator = new TicketValidator();
$ticketController = new TicketController($ticketService, $ticketValidator);

// Repositorios, Servicios, Validadores y Controladores para Comentarios de Ticket
$comentarioTicketRepository = new ComentarioTicketRepository($databaseConnection);
$comentarioTicketService = new ComentarioTicketService($comentarioTicketRepository);
$comentarioTicketValidator = new ComentarioTicketValidator();
$comentarioTicketController = new ComentarioTicketController($comentarioTicketService, $comentarioTicketValidator);


$corsMiddleware = new CorsMiddleware();
$jsonMiddleware = new JsonMiddleware();
$authMiddleware = new AuthMiddleware();

$corsMiddleware->handle($request);
$jsonMiddleware->handle($request);

$protectedMiddlewares = [$authMiddleware];

require BASE_PATH . '/routes/api.php';

$router->dispatch($request);

?>