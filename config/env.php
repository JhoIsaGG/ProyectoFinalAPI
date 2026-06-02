<?php

if(!function_exists("LoadEnv")){
    function LoadEnv(string $path): void {

        if(!is_file($path) || !is_readable($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if($lines === false) {
            return;
        }

        foreach($lines as $line) {
            $trimmedLine = trim($line);

            if($trimmedLine === "" || str_starts_with ($trimmedLine, "#")) {
                continue;
            }

            [$key, $value] = array_pad(explode("=", $trimmedLine, 2), 2, "");
            $key = trim($key);
            if ($key === "") {
                continue;
            }
            
            $value = trim($value, "\"'");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
            putenv(sprintf("%s=%s", $key, $value));

        }
    }
}
