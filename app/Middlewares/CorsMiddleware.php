<?php

declare(strict_types=1);

class CorsMiddleware implements Middleware
{
    public function handle(Request $request): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($request->getMethod() === 'OPTIONS') {
            Response::noContent();
            exit;
        }
    }
}
