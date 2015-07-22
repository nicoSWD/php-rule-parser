<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
error_reporting(-1);
ini_set('display_errors', 1);

spl_autoload_register(function ($class) {
    if (strpos($class, 'nicoSWD') !== 0) {
        return null;
    }

    $class = str_replace('\\', DIRECTORY_SEPARATOR, ltrim(str_replace('nicoSWD\Rules', '', $class), '\\'));
    return require __DIR__ . '/../src/' . $class . '.php';
});
