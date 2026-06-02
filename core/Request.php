<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Request
|--------------------------------------------------------------------------
| Esta clase representa la peticion HTTP que llega desde el cliente. En un
| proyecto MVC tradicional el flujo normalmente termina en una vista HTML,
| pero en una API necesitamos entender el metodo, la ruta, los headers y el
| cuerpo JSON para responder correctamente.
|
| Problema que resuelve:
| - Evita leer $_SERVER, $_GET y php://input en muchas partes del codigo.
| - Centraliza la lectura de datos de la peticion.
| - Hace que Controller y Middleware trabajen con una interfaz clara.
*/

class Request
{
    private string $method;
    private string $uri;
    private array $headers;
    private array $queryParams;
    private array $body;
    private array $routeParams = [];

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->uri = $_SERVER['REQUEST_URI'] ?? '/';
        $this->headers = $this->captureHeaders();
        $this->queryParams = $_GET;
        $this->body = $this->captureJsonBody();
    }

    /*
    |----------------------------------------------------------------------
    | Captura de headers
    |----------------------------------------------------------------------
    | En PHP algunas instalaciones tienen getallheaders() y otras no. Este
    | metodo crea una forma compatible de obtener los encabezados.
    */
    private function captureHeaders(): array
    {
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            return is_array($headers) ? $headers : [];
        }

        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (!str_starts_with($key, 'HTTP_')) {
                continue;
            }

            $headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
            $headers[$headerName] = $value;
        }

        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['Content-Type'] = $_SERVER['CONTENT_TYPE'];
        }

        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $headers['Content-Length'] = $_SERVER['CONTENT_LENGTH'];
        }

        return $headers;
    }

    /*
    |----------------------------------------------------------------------
    | Lectura del body JSON
    |----------------------------------------------------------------------
    | Las APIs REST suelen recibir datos como JSON en lugar de formularios.
    | Este metodo decodifica el cuerpo y devuelve un arreglo listo para usar.
    */
    private function captureJsonBody(): array
    {
        $rawBody = file_get_contents('php://input');

        if ($rawBody === false || trim($rawBody) === '') {
            return [];
        }

        $decodedBody = json_decode($rawBody, true);

        return is_array($decodedBody) ? $decodedBody : [];
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getHeader(string $name): ?string
    {
        foreach ($this->headers as $headerName => $headerValue) {
            if (strcasecmp($headerName, $name) === 0) {
                return $headerValue;
            }
        }

        return null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function setRouteParams(array $routeParams): void
    {
        $this->routeParams = $routeParams;
    }

    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    public function getParam(string $key, mixed $default = null): mixed
    {
        return $this->routeParams[$key]
            ?? $this->queryParams[$key]
            ?? $this->body[$key]
            ?? $default;
    }
}