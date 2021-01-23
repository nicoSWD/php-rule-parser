<?php

if (is_file($autoLoader = __DIR__ . '/../../../vendor/autoload.php')) {
    require_once $autoLoader;
} else {
    spl_autoload_register(function (string $className): void {
        if (str_starts_with($className, 'nicoSWD\\Rule\\')) {
            if (is_file($file = __DIR__ . '/' . str_replace('\\', '/', substr($className, 13)) . '.php')) {
                require $file;
            }
        }
    });
}
