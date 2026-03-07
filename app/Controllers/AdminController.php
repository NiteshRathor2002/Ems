<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Response;
use App\Core\Validator;
use App\Models\User;

class AdminController extends Controller
{
    public function loginPage(): void
    {
        if (Auth::check() && Auth::isAdmin()) {
            Response::redirect('/Ems/public/admin/employees');
        }

        $this->render('admin/login', ['title' => 'Admin Login']);
    }

    public function login(): void
    {
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            Response::json(['ok' => false, 'message' => 'Invalid CSRF token'], 419);
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if (!Validator::email($email) || $password === '') {
            Response::json(['ok' => false, 'message' => 'Email and password are required'], 422);
        }

        $user = (new User($this->config))->findByEmail($email);
        if (!$user || !(bool) $user['is_admin'] || !password_verify($password, $user['password_hash'])) {
            Response::json(['ok' => false, 'message' => 'Invalid admin credentials'], 401);
        }

        Auth::login((int) $user['id'], true);
        Response::json(['ok' => true, 'redirect' => '/Ems/public/admin/employees']);
    }

    public function employees(): void
    {
        if (!Auth::check() || !Auth::isAdmin()) {
            Response::redirect('/Ems/public/admin/login');
        }

        $employees = (new User($this->config))->allEmployees();
        $this->render('admin/employees', ['title' => 'Employees', 'employees' => $employees]);
    }

    public function logout(): void
    {
        if (Csrf::verify($_POST['_csrf'] ?? null)) {
            Auth::logout();
        }
        Response::redirect('/Ems/public/admin/login');
    }
}
