<?php use App\Core\Csrf; ?>
<div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h4 mb-3">Admin Login</h1>
                <form id="adminLoginForm" autocomplete="off" class="needs-validation" novalidate>
                    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input class="form-control" type="email" name="email" required>
                        <div class="invalid-feedback">Please enter a valid email.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input class="form-control" type="password" name="password" required>
                        <div class="invalid-feedback">Password is required.</div>
                    </div>

                    <button class="btn btn-dark w-100" type="submit">Login</button>
                    <div id="adminLoginMessage" class="form-text mt-2"></div>
                </form>
            </div>
        </div>
    </div>
</div>
