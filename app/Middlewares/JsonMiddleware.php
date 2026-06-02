<?php

declare(strict_types=1);


class JsonMiddleware implements Middleware
{
    public function handle(Request $request): void
    {
        header('Content-Type: application/json; charset=utf-8');
    }
}
