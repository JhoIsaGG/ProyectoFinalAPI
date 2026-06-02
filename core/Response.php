<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Response
|--------------------------------------------------------------------------
| Esta clase concentra la salida HTTP. En lugar de mezclar echo, header()
| y http_response_code() por todo el proyecto, aqui generamos respuestas
| JSON de forma uniforme.
|
| Problema que resuelve:
| - Mantiene consistencia en todas las respuestas de la API.
| - Facilita trabajar con codigos HTTP.
| - Refuerza la idea de que una API devuelve datos, no vistas HTML.
*/

class Response
{
    public static function json(array $data, int $statusCode = 200, array $headers = []): void
    {
        http_response_code($statusCode);

        header('Content-Type: application/json; charset=utf-8');

        foreach ($headers as $name => $value) {
            header(sprintf('%s: %s', $name, $value));
        }

        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public static function noContent(): void
    {
        http_response_code(204);
    }
}