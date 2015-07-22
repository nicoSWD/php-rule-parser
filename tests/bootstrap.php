<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

spl_autoload_register(function ($class) {
    if (strpos($class, 'nicoSWD') !== 0) {
        return;
    }

    return require __DIR__ . '/../src/' . str_replace('\\', '/', ltrim(str_replace('nicoSWD\Rules', '', $class), '\\')) . '.php';
});
