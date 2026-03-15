<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Auth;

class HomeController extends Controller
{
    public function index(): void
    {
        $base = (string) ($this->config['base_path'] ?? '/Ems/public');

        if (Auth::check()) {
            if (Auth::isAdmin()) {
                Response::redirect($base . '/admin/dashboard');
            }
            Response::redirect($base . '/dashboard');
        }

        Response::redirect($base . '/login');
    }
}
