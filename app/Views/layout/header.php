<?php

use App\Core\Csrf;
use App\Core\Auth;

$base = '/Ems/public';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'EMS', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
</head>
<body>
<header class="topbar">
    <div class="container topbar-inner">
        <a href="<?= $base ?>/" class="brand">EMS Core PHP</a>
        <nav>
            <?php if (Auth::check()): ?>
                <?php if (Auth::isAdmin()): ?>
                    <a href="<?= $base ?>/admin/employees">Employees</a>
                    <form method="post" action="<?= $base ?>/admin/logout" class="inline-form">
                        <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                        <button type="submit">Admin Logout</button>
                    </form>
                <?php else: ?>
                    <a href="<?= $base ?>/profile">Profile</a>
                    <form method="post" action="<?= $base ?>/logout" class="inline-form">
                        <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                        <button type="submit">Logout</button>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <a href="<?= $base ?>/signup">Sign Up</a>
                <a href="<?= $base ?>/login">Login</a>
                <a href="<?= $base ?>/admin/login">Admin</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container main-content">
