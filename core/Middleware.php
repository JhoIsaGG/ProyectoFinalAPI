<?php

declare(strict_types=1);

interface Middleware{
    public function handle (Request $request): void;
}

?>