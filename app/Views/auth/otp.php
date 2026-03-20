<?php use App\Core\Csrf; ?>
<div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h4 mb-2">Enter OTP</h1>
                <p class="text-muted mb-3">We sent a 4-digit OTP to <strong><?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?></strong>.</p>

                <form method="post" action="<?= $base ?>/forgot-password/otp" class="needs-validation" novalidate>
                    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">

                    <div class="mb-3">
                        <label class="form-label">4-Digit OTP</label>
                        <div class="d-flex gap-2">
                            <input class="form-control text-center" style="max-width:60px" type="text" name="otp1" maxlength="1" inputmode="numeric" required>
                            <input class="form-control text-center" style="max-width:60px" type="text" name="otp2" maxlength="1" inputmode="numeric" required>
                            <input class="form-control text-center" style="max-width:60px" type="text" name="otp3" maxlength="1" inputmode="numeric" required>
                            <input class="form-control text-center" style="max-width:60px" type="text" name="otp4" maxlength="1" inputmode="numeric" required>
                        </div>
                        <div class="form-text">Enter the code within 10 minutes.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input class="form-control" type="password" name="password" minlength="8" required>
                        <div class="invalid-feedback">Password must be at least 8 characters.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input class="form-control" type="password" name="password_confirm" minlength="8" required>
                        <div class="invalid-feedback">Please confirm your password.</div>
                    </div>

                    <button class="btn btn-dark w-100" type="submit">Verify &amp; Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
