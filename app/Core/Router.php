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
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        if ($scriptDir !== '/' && $scriptDir !== '.') {
            $path = preg_replace('#^' . preg_quote($scriptDir, '#') . '#', '', $path) ?: '/';
        }

        $path = $this->normalizePath($path);

        $match = $this->match($method, $path);
        if ($match === null) {
            http_response_code(404);
            echo '404 - Page not found';
            return;
        }

        [$handler, $params] = $match;
        [$controllerName, $action] = explode('@', $handler, 2);

        $fqcn = 'App\\Controllers\\' . $controllerName;
        if (!class_exists($fqcn)) {
            http_response_code(500);
            echo 'Controller not found';
            return;
        }

        $controller = new $fqcn($this->config);
        if (!method_exists($controller, $action)) {
            http_response_code(500);
            echo 'Action not found';
            return;
        }

        call_user_func_array([$controller, $action], $params);
    }

    private function addRoute(string $method, string $path, string $handler): void
    {
        $path = $this->normalizePath($path);

        if (strpos($path, '{') === false) {
            $this->routes[$method][] = [
                'type' => 'static',
                'path' => $path,
                'handler' => $handler,
            ];
            return;
        }

        $paramNames = [];
        $regex = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', static function (array $m) use (&$paramNames): string {
            $paramNames[] = $m[1];
            return '([^/]+)';
        }, $path);

        $this->routes[$method][] = [
            'type' => 'dynamic',
            'path' => $path,
            'regex' => '#^' . $regex . '$#',
            'params' => $paramNames,
            'handler' => $handler,
        ];
    }

    private function match(string $method, string $path): ?array
    {
        $candidates = $this->routes[$method] ?? [];

        foreach ($candidates as $route) {
            if ($route['type'] === 'static' && $route['path'] === $path) {
                return [$route['handler'], []];
            }
        }

        foreach ($candidates as $route) {
            if ($route['type'] !== 'dynamic') {
                continue;
            }
            if (!preg_match($route['regex'], $path, $matches)) {
                continue;
            }

            array_shift($matches);
            return [$route['handler'], $matches];
        }

        return null;
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . ltrim($path, '/');
        $path = rtrim($path, '/');
        return $path === '' ? '/' : $path;
    }
}
