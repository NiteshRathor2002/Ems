<?php

declare(strict_types=1);

$config = require __DIR__ . '/../config/config.php';

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    spl_autoload_register(function (string $class): void {
        $prefix = 'App\\';
        $baseDir = __DIR__ . '/../app/';
        if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
            return;
        }
        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    });
} else {
    require __DIR__ . '/../vendor/autoload.php';
}

use App\Core\Router;
use App\Core\Session;

Session::boot($config['session']['name']);

$router = new Router($config);

require __DIR__ . '/../routes/web.php';

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
