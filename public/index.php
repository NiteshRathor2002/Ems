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

$router->get('/', 'HomeController@index');
$router->get('/signup', 'AuthController@signupPage');
$router->post('/api/signup', 'AuthController@signup');
$router->get('/login', 'AuthController@loginPage');
$router->post('/api/login', 'AuthController@login');
$router->post('/logout', 'AuthController@logout');

$router->get('/profile', 'ProfileController@page');
$router->post('/api/profile/update', 'ProfileController@update');

$router->get('/admin/login', 'AdminController@loginPage');
$router->post('/api/admin/login', 'AdminController@login');
$router->post('/admin/logout', 'AdminController@logout');
$router->get('/admin/employees', 'AdminController@employees');

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
