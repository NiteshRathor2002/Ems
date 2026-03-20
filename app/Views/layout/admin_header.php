<?php

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;

$base = (string) ($config['base_path'] ?? (parse_url((string) ($config['app_url'] ?? ''), PHP_URL_PATH) ?: '/Ems/public'));
$reqPath = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');
if (str_starts_with($reqPath, $base)) {
    $reqPath = substr($reqPath, strlen($base)) ?: '/';
}

$isActive = static function (string $prefix) use ($reqPath): bool {
    return $reqPath === $prefix || str_starts_with($reqPath, $prefix . '/');
};
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'EMS Admin', ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
</head>
<body class="bg-light" data-base="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>">
<?php
$adminName = (string) Session::get('user_name', 'Mr. Admin');
$adminInitials = strtoupper(mb_substr(trim($adminName), 0, 1)) ?: 'A';
?>

<div class="ems-admin-shell">
    <aside class="ems-sidebar d-none d-md-flex flex-column">
        <div class="ems-sidebar-brand">
            <div class="ems-logo"><?= htmlspecialchars($adminInitials, ENT_QUOTES, 'UTF-8') ?></div>
            <div class="ms-2">
                <div class="fw-semibold">Employee</div>
                <div class="text-muted small">Management System</div>
            </div>
        </div>

        <div class="ems-sidebar-section text-uppercase small text-muted px-3 mt-2"></div>
        <ul class="nav flex-column px-2 gap-1">
            <li class="nav-item">
                <a class="nav-link ems-navlink <?= $isActive('/admin/dashboard') ? 'active' : '' ?>" href="<?= $base ?>/admin/dashboard">
                    <i class="bi bi-grid"></i><span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link ems-navlink disabled" href="#" tabindex="-1" aria-disabled="true">
                    <i class="bi bi-diagram-3"></i><span>Department</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link ems-navlink <?= $isActive('/admin/leaves') ? 'active' : '' ?>" href="<?= $base ?>/admin/leaves">
                    <i class="bi bi-calendar2-week"></i><span>Leave</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link ems-navlink <?= $isActive('/admin/employees') ? 'active' : '' ?>" href="<?= $base ?>/admin/employees">
                    <i class="bi bi-people"></i><span>Employee</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link ems-navlink disabled" href="#" tabindex="-1" aria-disabled="true">
                    <i class="bi bi-cash-stack"></i><span>Payroll</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link ems-navlink disabled" href="#" tabindex="-1" aria-disabled="true">
                    <i class="bi bi-check2-square"></i><span>Attendance</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link ems-navlink disabled" href="#" tabindex="-1" aria-disabled="true">
                    <i class="bi bi-bar-chart"></i><span>Report</span>
                </a>
            </li>
           
        </ul>

        <div class="mt-auto px-3 pb-3">
            <a class="btn btn-outline-secondary w-100" href="<?= $base ?>/admin/profile"><i class="bi bi-person me-1"></i>Profile</a>
            <form method="post" action="<?= $base ?>/admin/logout" class="mt-2">
                <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                <button type="submit" class="btn btn-light w-100"><i class="bi bi-box-arrow-right me-1"></i>Log out</button>
            </form>
        </div>
    </aside>

    <div class="ems-main">
        <header class="ems-topbar border-bottom">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary btn-sm d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebarOffcanvas" aria-controls="adminSidebarOffcanvas">
                    <i class="bi bi-list"></i>
                </button>

                <form class="d-none d-md-flex ems-topbar-search" method="get" action="<?= $base ?>/admin/employees">
                    <i class="bi bi-search"></i>
                    <input class="form-control form-control-sm" name="q" placeholder="Search employees..." value="<?= htmlspecialchars((string) ($_GET['q'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </form>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm" title="Notifications" disabled>
                    <i class="bi bi-bell"></i>
                </button>
                <?php if (Auth::check() && Auth::isAdmin()): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= $base ?>/admin/profile">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="post" action="<?= $base ?>/admin/logout" class="px-3 py-1 m-0">
                                    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                                    <button type="submit" class="btn btn-sm btn-danger w-100">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <main class="container-fluid py-4">
            <?php $success = Session::flash('success'); $error = Session::flash('error'); ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars((string) $success, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <div class="offcanvas offcanvas-start" tabindex="-1" id="adminSidebarOffcanvas" aria-labelledby="adminSidebarOffcanvasLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="adminSidebarOffcanvasLabel">EMS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-0">
                    <div class="ems-sidebar ems-sidebar-mobile">
                        <div class="ems-sidebar-brand px-3 pt-3">
                            <div class="ems-logo"><?= htmlspecialchars($adminInitials, ENT_QUOTES, 'UTF-8') ?></div>
                            <div class="ms-2">
                                <div class="fw-semibold">Employee</div>
                                <div class="text-muted small">Management System</div>
                            </div>
                        </div>
                        <ul class="nav flex-column px-2 gap-1 mt-2">
                            <li class="nav-item">
                                <a class="nav-link ems-navlink <?= $isActive('/admin/dashboard') ? 'active' : '' ?>" href="<?= $base ?>/admin/dashboard"><i class="bi bi-grid"></i><span>Dashboard</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link ems-navlink <?= $isActive('/admin/leaves') ? 'active' : '' ?>" href="<?= $base ?>/admin/leaves"><i class="bi bi-calendar2-week"></i><span>Leave</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link ems-navlink <?= $isActive('/admin/employees') ? 'active' : '' ?>" href="<?= $base ?>/admin/employees"><i class="bi bi-people"></i><span>Employee</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link ems-navlink" href="<?= $base ?>/admin/profile"><i class="bi bi-person"></i><span>Profile</span></a>
                            </li>
                        </ul>
                        <div class="px-3 pb-3 mt-auto">
                            <form method="post" action="<?= $base ?>/admin/logout">
                                <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                                <button type="submit" class="btn btn-light w-100"><i class="bi bi-box-arrow-right me-1"></i>Log out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
