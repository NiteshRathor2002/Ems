<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\User;

class ProfileController extends Controller
{
    public function show(): void
    {
        $base = (string) ($this->config['base_path'] ?? '/Ems/public');
        if (!Auth::check() || !Auth::isAdmin()) {
            Response::redirect($base . '/admin/login');
        }

        $user = (new User($this->config))->findById((int) Auth::userId());
        if (!$user || !(bool) ($user['is_admin'] ?? false)) {
            Auth::logout();
            Response::redirect($base . '/admin/login');
        }

        $this->render('admin/profile', [
            'title' => 'Admin Profile',
            'user' => $user,
        ], 'admin');
    }

    public function changePassword(): void
    {
        $base = (string) ($this->config['base_path'] ?? '/Ems/public');
        if (!Auth::check() || !Auth::isAdmin()) {
            Response::redirect($base . '/admin/login');
        }

        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            Session::flash('error', 'Invalid CSRF token.');
            Response::redirect($base . '/admin/profile');
        }

        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['password_confirm'] ?? '');

        if (!Validator::minLength($password, 8)) {
            Session::flash('error', 'Password must be at least 8 characters.');
            Response::redirect($base . '/admin/profile');
        }
        if ($password !== $confirm) {
            Session::flash('error', 'Password confirmation does not match.');
            Response::redirect($base . '/admin/profile');
        }

        (new User($this->config))->updatePassword((int) Auth::userId(), password_hash($password, PASSWORD_DEFAULT));
        Session::flash('success', 'Password updated.');
        Response::redirect($base . '/admin/profile');
    }
}

