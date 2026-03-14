<?php

use App\Core\Csrf;

$uploadPath = (string) (($config['upload']['profile_web_path'] ?? '/files/profile/'));
$totalEmployees = (int) ($totalEmployees ?? 0);
$newEmployees = (int) ($newEmployees ?? 0);
$resultCount = is_array($employees ?? null) ? count($employees) : 0;
?>
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <div class="text-muted small">Home / Employee</div>
            <h1 class="h5 mb-0">Employee</h1>
        </div>
        <a class="btn btn-primary" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/admin/employees/create">
            <i class="bi bi-person-plus me-1"></i>Add Employee
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-4 col-xl-3">
        <div class="card ems-stat ems-stat-blue border-0 shadow-sm">
            <div class="card-body">
                <div class="ems-stat-icon"><i class="bi bi-people"></i></div>
                <div class="small opacity-75">Total Employees</div>
                <div class="display-6 fw-semibold mb-0"><?= $totalEmployees ?></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 col-xl-3">
        <div class="card ems-stat ems-stat-purple border-0 shadow-sm">
            <div class="card-body">
                <div class="ems-stat-icon"><i class="bi bi-plus-square"></i></div>
                <div class="small opacity-75">New (Last 7 Days)</div>
                <div class="display-6 fw-semibold mb-0"><?= $newEmployees ?></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 col-xl-3">
        <div class="card ems-stat ems-stat-dark border-0 shadow-sm">
            <div class="card-body">
                <div class="ems-stat-icon"><i class="bi bi-funnel"></i></div>
                <div class="small opacity-75">Showing</div>
                <div class="display-6 fw-semibold mb-0"><?= $resultCount ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
            <div class="text-muted small">
                <?php if (trim((string) ($q ?? '')) !== ''): ?>
                    Results for: <span class="fw-semibold"><?= htmlspecialchars((string) $q, ENT_QUOTES, 'UTF-8') ?></span>
                <?php else: ?>
                    Latest employees
                <?php endif; ?>
            </div>
            <div class="d-md-none w-100">
                <form class="row g-2" method="get" action="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/admin/employees">
                    <div class="col-12">
                        <input class="form-control" type="text" name="q" value="<?= htmlspecialchars((string) ($q ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Search by name, email, or ID">
                    </div>
                    <div class="col-6">
                        <button class="btn btn-primary w-100" type="submit"><i class="bi bi-search me-1"></i>Search</button>
                    </div>
                    <div class="col-6">
                        <a class="btn btn-outline-secondary w-100" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/admin/employees">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th style="width: 70px;">Photo</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th style="width: 80px;">Age</th>
                        <th>Permanent</th>
                        <th>Current</th>
                        <th class="text-end" style="width: 220px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($employees)): ?>
                        <tr><td colspan="8" class="text-muted">No employees found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($employees as $emp): ?>
                            <tr>
                                <td><?= (int) $emp['id'] ?></td>
                                <td>
                                    <?php if (!empty($emp['profile_picture'])): ?>
                                        <img src="<?= htmlspecialchars($base . $uploadPath . rawurlencode($emp['profile_picture']), ENT_QUOTES, 'UTF-8') ?>" alt="Profile" class="rounded-circle object-fit-cover" style="width:40px;height:40px;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-secondary-subtle d-inline-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                            <span class="text-secondary small">&mdash;</span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($emp['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($emp['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= (int) $emp['age'] ?></td>
                                <td><?= htmlspecialchars($emp['perm_city'] . ', ' . $emp['perm_state'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($emp['curr_city'] . ', ' . $emp['curr_state'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="text-end">
                                    <div class="d-inline-flex flex-wrap gap-1 justify-content-end">
                                        <a class="btn btn-sm btn-outline-dark" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/admin/employees/show/<?= (int) $emp['id'] ?>">View</a>
                                        <a class="btn btn-sm btn-outline-secondary" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/admin/employees/edit/<?= (int) $emp['id'] ?>">Edit</a>
                                        <form method="post" action="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/admin/employees/delete/<?= (int) $emp['id'] ?>" class="m-0" onsubmit="return confirm('Delete this employee?');">
                                            <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
