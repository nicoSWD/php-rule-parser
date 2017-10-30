<?php

if (is_file($autoLoader = __DIR__.'/../../../vendor/autoload.php')) {
    require_once $autoLoader;
} else {
    spl_autoload_register(function (string $className) {
        if (strncmp('nicoSWD\\Rules\\', $className, 14) === 0) {
            if (is_file($file = __DIR__.'/'.str_replace('\\', '/', substr($className, 14)).'.php')) {
                require $file;
            }
        }
    });
}
