<?php

use App\Core\Csrf;

$employee = $employee ?? [];
$uploadPath = (string) (($config['upload']['profile_web_path'] ?? '/files/profile/'));
$employeeId = (int) ($employee['id'] ?? 0);
?>

<div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
    <div>
        <div class="text-muted small">Home / Employee / Details</div>
        <h1 class="h5 mb-0">Employee Details</h1>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/admin/employees"><i class="bi bi-arrow-left me-1"></i>Back</a>
        <a class="btn btn-outline-dark" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/admin/employees/edit/<?= $employeeId ?>"><i class="bi bi-pencil me-1"></i>Edit</a>
        <form method="post" action="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/admin/employees/delete/<?= $employeeId ?>" class="m-0" onsubmit="return confirm('Delete this employee?');">
            <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
            <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i>Delete</button>
        </form>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-xl-9">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <div class="text-center mb-3">
                    <?php if (!empty($employee['profile_picture'])): ?>
                        <img src="<?= htmlspecialchars($base . $uploadPath . rawurlencode((string) $employee['profile_picture']), ENT_QUOTES, 'UTF-8') ?>" alt="Profile" class="rounded-circle border object-fit-cover" style="width:140px;height:140px;">
                    <?php else: ?>
                        <div class="rounded-circle bg-secondary-subtle d-inline-flex align-items-center justify-content-center border" style="width:140px;height:140px;">
                            <span class="text-secondary">No photo</span>
                        </div>
                    <?php endif; ?>
                    <div class="mt-2 fw-semibold"><?= htmlspecialchars((string) ($employee['full_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="text-muted small"><?= htmlspecialchars((string) ($employee['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-3">
                        <div class="text-muted small">Age</div>
                        <div class="fw-semibold"><?= (int) ($employee['age'] ?? 0) ?></div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="text-muted small">Department</div>
                        <div class="fw-semibold"><?= htmlspecialchars((string) ($employee['department'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="text-muted small">Created</div>
                        <div class="fw-semibold"><?= htmlspecialchars((string) ($employee['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="text-muted small">Updated</div>
                        <div class="fw-semibold"><?= htmlspecialchars((string) ($employee['updated_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h2 class="h6">Permanent Address</h2>
                        <div class="text-muted">
                            <?= htmlspecialchars((string) ($employee['perm_line1'] ?? ''), ENT_QUOTES, 'UTF-8') ?><br>
                            <?php if (!empty($employee['perm_line2'])): ?>
                                <?= htmlspecialchars((string) $employee['perm_line2'], ENT_QUOTES, 'UTF-8') ?><br>
                            <?php endif; ?>
                            <?= htmlspecialchars((string) ($employee['perm_city'] ?? ''), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars((string) ($employee['perm_state'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h2 class="h6">Current Address</h2>
                        <div class="text-muted">
                            <?= htmlspecialchars((string) ($employee['curr_line1'] ?? ''), ENT_QUOTES, 'UTF-8') ?><br>
                            <?php if (!empty($employee['curr_line2'])): ?>
                                <?= htmlspecialchars((string) $employee['curr_line2'], ENT_QUOTES, 'UTF-8') ?><br>
                            <?php endif; ?>
                            <?= htmlspecialchars((string) ($employee['curr_city'] ?? ''), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars((string) ($employee['curr_state'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
