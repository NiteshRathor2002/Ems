<?php

use App\Core\Csrf;
use App\Core\Auth;
use App\Core\Session;

$base = (string) ($config['base_path'] ?? (parse_url((string) ($config['app_url'] ?? ''), PHP_URL_PATH) ?: '/Ems/public'));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'EMS', ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
</head>
<body data-base="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= $base ?>/">EMS Core PHP</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarDefault" aria-controls="navbarDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarDefault">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if (Auth::check() && !Auth::isAdmin()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= $base ?>/profile">Profile</a></li>
                <?php endif; ?>
            </ul>
            <div class="d-flex gap-2 align-items-center">
                <?php if (Auth::check()): ?>
                    <?php if (Auth::isAdmin()): ?>
                        <a class="btn btn-outline-light btn-sm" href="<?= $base ?>/admin/dashboard">Dashboard</a>
                        <a class="btn btn-outline-light btn-sm" href="<?= $base ?>/admin/employees">Employees</a>
                        <form method="post" action="<?= $base ?>/admin/logout" class="m-0">
                            <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                            <button type="submit" class="btn btn-light btn-sm">Logout</button>
                        </form>
                    <?php else: ?>
                        <form method="post" action="<?= $base ?>/logout" class="m-0">
                            <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                            <button type="submit" class="btn btn-light btn-sm">Logout</button>
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    <a class="btn btn-outline-light btn-sm" href="<?= $base ?>/signup">Sign Up</a>
                    <a class="btn btn-outline-light btn-sm" href="<?= $base ?>/login">Login</a>
                    <a class="btn btn-light btn-sm" href="<?= $base ?>/admin/login">Admin</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<main class="container py-4">
    <?php $success = Session::flash('success'); $error = Session::flash('error'); ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars((string) $success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
