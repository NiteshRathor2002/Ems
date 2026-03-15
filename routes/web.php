<?php

declare(strict_types=1);

/**
 * @var \App\Core\Router $router
 */

$router->get('/', 'HomeController@index');

// Employee self-service (optional)
$router->get('/signup', 'AuthController@signupPage');
$router->post('/api/signup', 'AuthController@signup');
$router->get('/login', 'AuthController@loginPage');
$router->post('/api/login', 'AuthController@login');
$router->post('/logout', 'AuthController@logout');
$router->get('/dashboard', 'ProfileController@page');
$router->get('/profile', 'ProfileController@page');
$router->post('/api/profile/update', 'ProfileController@update');
$router->get('/leave', 'LeaveController@index');
$router->post('/leave/apply', 'LeaveController@apply');

// Files
$router->get('/files/profile/{file}', 'FileController@profile');

// Admin
$router->get('/admin/login', 'AdminController@loginPage');
$router->post('/api/admin/login', 'AdminController@login');
$router->post('/admin/logout', 'AdminController@logout');

$router->get('/admin/dashboard', 'Admin\\DashboardController@index');
$router->get('/admin/profile', 'Admin\\ProfileController@show');
$router->post('/admin/profile/password', 'Admin\\ProfileController@changePassword');

$router->get('/admin/employees', 'Admin\\EmployeeController@index');
$router->get('/admin/employees/create', 'Admin\\EmployeeController@create');
$router->post('/admin/employees/create', 'Admin\\EmployeeController@store');
$router->get('/admin/employees/show/{id}', 'Admin\\EmployeeController@show');
$router->get('/admin/employees/edit/{id}', 'Admin\\EmployeeController@edit');
$router->post('/admin/employees/edit/{id}', 'Admin\\EmployeeController@update');
$router->post('/admin/employees/delete/{id}', 'Admin\\EmployeeController@destroy');
$router->get('/admin/leaves', 'Admin\\LeaveController@index');
$router->post('/admin/leaves/approve/{id}', 'Admin\\LeaveController@approve');
$router->post('/admin/leaves/reject/{id}', 'Admin\\LeaveController@reject');
