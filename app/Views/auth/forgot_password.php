<?php use App\Core\Csrf; ?>
<div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h4 mb-3">Forgot Password</h1>
                <p class="text-muted mb-4">Enter your email to receive a one-time password (OTP).</p>

                <form method="post" action="<?= $base ?>/employee/forgot" class="needs-validation" novalidate>
                    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-3">
                            <tbody>
                                <tr>
                                    <th style="width: 35%;">Email Address</th>
                                    <td>
                                        <input class="form-control" type="email" name="email" required>
                                        <div class="invalid-feedback">Please enter a valid email.</div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Action</th>
                                    <td>
                                        <button class="btn btn-dark" type="submit">Send OTP</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
