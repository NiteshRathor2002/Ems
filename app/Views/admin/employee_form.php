<?php

use App\Core\Csrf;

$mode = (string) ($mode ?? 'create');
$employee = $employee ?? null;
$isEdit = $mode === 'edit' && is_array($employee);
$uploadPath = (string) (($config['upload']['profile_web_path'] ?? '/files/profile/'));

$action = $isEdit
    ? ($base . '/admin/employees/edit/' . (int) ($employee['id'] ?? 0))
    : ($base . '/admin/employees/create');
?>

<div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
    <div>
        <div class="text-muted small">Home / Employee / <?= $isEdit ? 'Edit' : 'Create' ?></div>
        <h1 class="h5 mb-0"><?= $isEdit ? 'Edit Employee' : 'Add Employee' ?></h1>
    </div>
    <a class="btn btn-outline-secondary" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/admin/employees"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form method="post" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>" enctype="multipart/form-data" autocomplete="off" class="needs-validation" novalidate>
            <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">Full Name</label>
                    <input class="form-control" type="text" name="full_name" maxlength="120" required value="<?= htmlspecialchars((string) ($employee['full_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    <div class="invalid-feedback">Please enter full name.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Email</label>
                    <input class="form-control" type="email" name="email" required value="<?= htmlspecialchars((string) ($employee['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    <div class="invalid-feedback">Please enter a valid email.</div>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Age</label>
                    <input class="form-control" type="number" name="age" min="18" max="80" required value="<?= htmlspecialchars((string) ($employee['age'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    <div class="invalid-feedback">Age must be between 18 and 80.</div>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Salary</label>
                    <input class="form-control" type="number" name="salary" min="1" max="100000000" step="1" required value="<?= htmlspecialchars((string) ($employee['salary'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="e.g. 50000">
                    <div class="invalid-feedback">Please enter salary.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Department</label>
                    <input class="form-control" type="text" name="department" maxlength="120" required value="<?= htmlspecialchars((string) ($employee['department'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="e.g. HR, Sales, Engineering">
                    <div class="invalid-feedback">Please enter department.</div>
                </div>
                <div class="col-12 col-md-9">
                    <label class="form-label"><?= $isEdit ? 'New Password (optional)' : 'Password' ?></label>
                    <input class="form-control" type="password" name="password" <?= $isEdit ? '' : 'required' ?> minlength="8" placeholder="<?= $isEdit ? 'Leave blank to keep current password' : '' ?>">
                    <?php if (!$isEdit): ?>
                        <div class="invalid-feedback">Password must be at least 8 characters.</div>
                    <?php endif; ?>
                </div>

                <div class="col-12">
                    <label class="form-label">Profile Picture</label>
                    <input class="form-control" type="file" name="profile_picture" accept=".jpg,.jpeg,.png,.webp" data-max-size="2097152">
                    <div class="form-text">Allowed: jpg/png/webp. Max size: 2MB.</div>
                    <?php if ($isEdit && !empty($employee['profile_picture'])): ?>
                        <div class="mt-2 text-center">
                            <img id="employeeProfilePreview" src="<?= htmlspecialchars($base . $uploadPath . rawurlencode((string) $employee['profile_picture']), ENT_QUOTES, 'UTF-8') ?>" alt="Profile" class="rounded-circle border object-fit-cover" style="width:110px;height:110px;">
                        </div>
                    <?php else: ?>
                        <div class="mt-2 text-center d-none" id="employeeProfilePreviewWrap">
                            <img id="employeeProfilePreview" src="" alt="Preview" class="rounded-circle border object-fit-cover" style="width:110px;height:110px;">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-12"><hr class="my-2"></div>

                <div class="col-12">
                    <h2 class="h6 mb-2">Permanent Address</h2>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Line 1</label>
                    <input class="form-control" type="text" name="perm_line1" required value="<?= htmlspecialchars((string) ($employee['perm_line1'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    <div class="invalid-feedback">Required.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Line 2</label>
                    <input class="form-control" type="text" name="perm_line2" value="<?= htmlspecialchars((string) ($employee['perm_line2'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">City</label>
                    <input class="form-control" type="text" name="perm_city" required value="<?= htmlspecialchars((string) ($employee['perm_city'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    <div class="invalid-feedback">Required.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">State</label>
                    <?php $states = ['Rajasthan','Maharashtra','Delhi','Uttar Pradesh','Gujarat']; ?>
                    <select class="form-select" name="perm_state" required>
                        <option value="">Select</option>
                        <?php foreach ($states as $st): ?>
                            <option value="<?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?>" <?= ($employee['perm_state'] ?? '') === $st ? 'selected' : '' ?>><?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Please select a state.</div>
                </div>

                <div class="col-12">
                    <h2 class="h6 mb-2 mt-2">Current Address</h2>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Line 1</label>
                    <input class="form-control" type="text" name="curr_line1" required value="<?= htmlspecialchars((string) ($employee['curr_line1'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    <div class="invalid-feedback">Required.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Line 2</label>
                    <input class="form-control" type="text" name="curr_line2" value="<?= htmlspecialchars((string) ($employee['curr_line2'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">City</label>
                    <input class="form-control" type="text" name="curr_city" required value="<?= htmlspecialchars((string) ($employee['curr_city'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    <div class="invalid-feedback">Required.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">State</label>
                    <select class="form-select" name="curr_state" required>
                        <option value="">Select</option>
                        <?php foreach ($states as $st): ?>
                            <option value="<?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?>" <?= ($employee['curr_state'] ?? '') === $st ? 'selected' : '' ?>><?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Please select a state.</div>
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                    <button class="btn btn-dark" type="submit"><?= $isEdit ? 'Save Changes' : 'Create Employee' ?></button>
                    <?php if ($isEdit): ?>
                        <a class="btn btn-outline-dark" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/admin/employees/show/<?= (int) ($employee['id'] ?? 0) ?>">Cancel</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>
