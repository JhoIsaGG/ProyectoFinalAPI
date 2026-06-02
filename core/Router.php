<?php

declare(strict_types=1);

class Router
{
    private array $routes = [];
    private string $basePath;

    public function __construct(?string $basePath = null)
    {
        $this->basePath = $basePath ?? $this->detectBasePath();
    }

    public function add(string $method, string $path, callable|array $handler, array $middlewares = []): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
            'middlewares' => $middlewares,
        ];
    }

    public function get(string $path, callable|array $handler, array $middlewares = []): void
    {
        $this->add('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, callable|array $handler, array $middlewares = []): void
    {
        $this->add('POST', $path, $handler, $middlewares);
    }

    public function put(string $path, callable|array $handler, array $middlewares = []): void
    {
        $this->add('PUT', $path, $handler, $middlewares);
    }

    public function delete(string $path, callable|array $handler, array $middlewares = []): void
    {
        $this->add('DELETE', $path, $handler, $middlewares);
    }
 private function detectBasePath(): string
    {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $scriptDirectory = str_replace('\\', '/', dirname($scriptName));

        if ($scriptDirectory === '/' || $scriptDirectory === '.') {
            return '';
        }

        return rtrim($scriptDirectory, '/');
    }

    private function normalizePath(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = str_replace('\\', '/', $path);

        if ($this->basePath !== '' && str_starts_with($path, $this->basePath)) {
            $path = substr($path, strlen($this->basePath));
        }

        $path = '/' . trim($path, '/');

        return $path === '/' ? $path : rtrim($path, '/');
    }

    private function matchRoute(string $routePath, string $requestPath): array|false
    {
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $routePath);

        if ($pattern === null) {
            return false;
        }

        $pattern = '#^' . rtrim($pattern, '/') . '$#';

        if (!preg_match($pattern, rtrim($requestPath, '/'), $matches)) {
            return false;
        }

        $params = [];

        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    public function dispatch(Request $request): void
    {
        $requestMethod = $request->getMethod();
        $requestPath = $this->normalizePath($request->getUri());

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            $routeParams = $this->matchRoute($route['path'], $requestPath);

            if ($routeParams === false) {
                continue;
            }

            $request->setRouteParams($routeParams);

            foreach ($route['middlewares'] as $middleware) {
                $middleware->handle($request);
            }

            $handler = $route['handler'];

            if (is_array($handler)) {
                [$controller, $method] = $handler;
                $controller->$method($request);
                return;
            }

            $handler($request);
            return;
        }

        Response::json(
            [
                'success' => false,
                'message' => 'Ruta no encontrada',
                'path' => $requestPath,
            ],
            404
        );
    }
}

?>