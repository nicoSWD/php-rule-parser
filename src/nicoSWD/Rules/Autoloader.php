<?php

spl_autoload_register(function ($className) {
    if (strncmp('nicoSWD\\Rules\\', $className, 14) === 0) {
        $file = __DIR__ . '/' . str_replace('\\', '/', substr($className, 14)) . '.php';

        if (is_file($file)) {
            require $file;
        }
    }
});
