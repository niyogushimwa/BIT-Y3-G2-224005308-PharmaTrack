<?php
declare(strict_types=1);

/**
 * Tiny PSR-4 style autoloader for the App\ namespace.
 * App\Core\Auth  ->  src/Core/Auth.php
 */
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file     = dirname(__DIR__) . DIRECTORY_SEPARATOR
              . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';

    if (is_file($file)) {
        require $file;
    }
});
