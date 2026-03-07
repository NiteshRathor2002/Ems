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
        if (Auth::check()) {
            Response::redirect('/Ems/public/profile');
        }

        Response::redirect('/Ems/public/login');
    }
}
