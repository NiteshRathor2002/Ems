<?php

use App\Core\Session;

$recentEmployees = $recentEmployees ?? [];
$uploadPath = (string) (($config['upload']['profile_web_path'] ?? '/files/profile/'));
$adminName = (string) Session::get('user_name', 'Mr. Admin');
$adminInitial = strtoupper(mb_substr(trim($adminName), 0, 1)) ?: 'A';
$today = date('D, d M Y');
?>

<div class="card shadow-sm border-0 mb-3">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <div class="ems-logo" style="width:52px;height:52px;"><?= htmlspecialchars($adminInitial, ENT_QUOTES, 'UTF-8') ?></div>
            <div>
                <div class="fw-semibold">Welcome, <?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?>!</div>
                <div class="text-muted small">Home / Dashboard</div>
            </div>
        </div>
        <div class="text-muted small"><i class="bi bi-calendar3 me-1"></i><?= htmlspecialchars($today, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-4 col-xl-3">
        <div class="card ems-stat ems-stat-blue border-0 shadow-sm">
            <div class="card-body">
                <div class="ems-stat-icon"><i class="bi bi-people"></i></div>
                <div class="small opacity-75">Employees</div>
                <div class="display-6 fw-semibold mb-0"><?= (int) ($totalEmployees ?? 0) ?></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 col-xl-3">
        <div class="card ems-stat ems-stat-dark border-0 shadow-sm">
            <div class="card-body">
                <div class="ems-stat-icon"><i class="bi bi-mortarboard"></i></div>
                <div class="small opacity-75">Departments</div>
                <div class="display-6 fw-semibold mb-0">0</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 col-xl-3">
        <div class="card ems-stat ems-stat-orange border-0 shadow-sm">
            <div class="card-body">
                <div class="ems-stat-icon"><i class="bi bi-calendar2-week"></i></div>
                <div class="small opacity-75">Leave Type</div>
                <div class="display-6 fw-semibold mb-0">0</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 col-xl-3">
        <div class="card ems-stat ems-stat-purple border-0 shadow-sm">
            <div class="card-body">
                <div class="ems-stat-icon"><i class="bi bi-plus-square"></i></div>
                <div class="small opacity-75">New Employees (7d)</div>
                <div class="display-6 fw-semibold mb-0"><?= (int) ($newEmployees ?? 0) ?></div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4 col-xl-3">
        <div class="card ems-stat ems-stat-orange border-0 shadow-sm">
            <div class="card-body">
                <div class="ems-stat-icon"><i class="bi bi-send"></i></div>
                <div class="small opacity-75">New Leave Request</div>
                <div class="display-6 fw-semibold mb-0">0</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 col-xl-3">
        <div class="card ems-stat ems-stat-red border-0 shadow-sm">
            <div class="card-body">
                <div class="ems-stat-icon"><i class="bi bi-x-square"></i></div>
                <div class="small opacity-75">Rejected Leaves</div>
                <div class="display-6 fw-semibold mb-0">0</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 col-xl-3">
        <div class="card ems-stat ems-stat-green border-0 shadow-sm">
            <div class="card-body">
                <div class="ems-stat-icon"><i class="bi bi-check2-square"></i></div>
                <div class="small opacity-75">Approved Leaves</div>
                <div class="display-6 fw-semibold mb-0">0</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 col-xl-3">
        <div class="card ems-stat ems-stat-dark border-0 shadow-sm">
            <div class="card-body">
                <div class="ems-stat-icon"><i class="bi bi-person-check"></i></div>
                <div class="small opacity-75">Active Employees</div>
                <div class="display-6 fw-semibold mb-0"><?= (int) ($activeEmployees ?? 0) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 class="h6 mb-0">Recent Employees</h2>
            <a class="btn btn-sm btn-outline-dark" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/admin/employees">View all</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:70px;">ID</th>
                        <th style="width:70px;">Photo</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Email</th>
                        <th style="width:90px;">Age</th>
                        <th style="width:180px;">Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentEmployees)): ?>
                        <tr><td colspan="7" class="text-muted">No recent employees.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentEmployees as $emp): ?>
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
                                <td class="text-muted small"><?= htmlspecialchars((string) ($emp['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
