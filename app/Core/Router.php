<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = ['GET' => [], 'POST' => []];

    public function __construct(private array $config)
    {
    }

    public function get(string $path, string $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, string $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        if ($scriptDir !== '/' && $scriptDir !== '.') {
            $path = preg_replace('#^' . preg_quote($scriptDir, '#') . '#', '', $path) ?: '/';
        }

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo '404 - Page not found';
            return;
        }

        [$controllerName, $action] = explode('@', $handler);
        $fqcn = 'App\\Controllers\\' . $controllerName;
        $controller = new $fqcn($this->config);
        $controller->$action();
    }
}
