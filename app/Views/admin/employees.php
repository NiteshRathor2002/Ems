<?php

use App\Core\Csrf;

$uploadPath = (string) (($config['upload']['profile_web_path'] ?? '/files/profile/'));
$totalEmployees = (int) ($totalEmployees ?? 0);
$newEmployees = (int) ($newEmployees ?? 0);
$resultCount = is_array($employees ?? null) ? count($employees) : 0;
$page = max(1, (int) ($page ?? 1));
$perPage = max(1, (int) ($perPage ?? 5));
$filteredTotal = (int) ($filteredTotal ?? $resultCount);
$totalPages = max(1, (int) ($totalPages ?? 1));
$from = $filteredTotal === 0 ? 0 : (($page - 1) * $perPage) + 1;
$to = $filteredTotal === 0 ? 0 : min($filteredTotal, (($page - 1) * $perPage) + $resultCount);
$employeesUrl = htmlspecialchars($base, ENT_QUOTES, 'UTF-8') . '/admin/employees';
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
                <div class="display-6 fw-semibold mb-0"><?= $filteredTotal === 0 ? 0 : htmlspecialchars($from . '-' . $to, ENT_QUOTES, 'UTF-8') ?></div>
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
                        <th>Department</th>
                        <th>Email</th>
                        <th style="width: 80px;">Age</th>
                        <th>Permanent</th>
                        <th>Current</th>
                        <th class="text-end" style="width: 220px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($employees)): ?>
                        <tr><td colspan="9" class="text-muted">No employees found.</td></tr>
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
                                <td><?= htmlspecialchars((string) ($emp['department'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
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

        <?php if ($totalPages > 1): ?>
            <?php
            $qs = [];
            if (trim((string) ($q ?? '')) !== '') {
                $qs['q'] = (string) $q;
            }
            $linkFor = static function (int $p) use ($employeesUrl, $qs): string {
                $qs2 = $qs;
                $qs2['page'] = $p;
                return $employeesUrl . '?' . htmlspecialchars(http_build_query($qs2), ENT_QUOTES, 'UTF-8');
            };
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            ?>
            <nav class="mt-3" aria-label="Employee pagination">
                <ul class="pagination justify-content-end mb-0">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $linkFor(max(1, $page - 1)) ?>" tabindex="<?= $page <= 1 ? '-1' : '0' ?>">Prev</a>
                    </li>

                    <?php if ($start > 1): ?>
                        <li class="page-item"><a class="page-link" href="<?= $linkFor(1) ?>">1</a></li>
                        <?php if ($start > 2): ?>
                            <li class="page-item disabled"><span class="page-link">…</span></li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($p = $start; $p <= $end; $p++): ?>
                        <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= $linkFor($p) ?>"><?= (int) $p ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($end < $totalPages): ?>
                        <?php if ($end < $totalPages - 1): ?>
                            <li class="page-item disabled"><span class="page-link">…</span></li>
                        <?php endif; ?>
                        <li class="page-item"><a class="page-link" href="<?= $linkFor($totalPages) ?>"><?= (int) $totalPages ?></a></li>
                    <?php endif; ?>

                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $linkFor(min($totalPages, $page + 1)) ?>" tabindex="<?= $page >= $totalPages ? '-1' : '0' ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>
