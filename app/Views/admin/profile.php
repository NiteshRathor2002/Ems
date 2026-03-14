<?php

use App\Core\Csrf;

$user = $user ?? [];
?>

<div class="mb-3">
    <h1 class="h4 mb-0">Admin Profile</h1>
    <div class="text-muted small">Account details and password</div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h2 class="h6">Details</h2>
                <div class="mb-2"><span class="text-muted">Name:</span> <?= htmlspecialchars((string) ($user['full_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                <div class="mb-2"><span class="text-muted">Email:</span> <?= htmlspecialchars((string) ($user['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                <div class="mb-0"><span class="text-muted">Created:</span> <?= htmlspecialchars((string) ($user['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h2 class="h6">Change Password</h2>
                <form method="post" action="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/admin/profile/password" class="needs-validation" novalidate>
                    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input class="form-control" type="password" name="password" minlength="8" required>
                        <div class="invalid-feedback">Password must be at least 8 characters.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input class="form-control" type="password" name="password_confirm" minlength="8" required>
                        <div class="invalid-feedback">Please confirm password.</div>
                    </div>
                    <button class="btn btn-dark" type="submit">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
