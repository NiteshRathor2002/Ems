<?php use App\Core\Csrf; ?>
<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <h1 class="h4 mb-1">Create Account</h1>
        <p class="text-muted mb-4">Register as an employee. All fields are validated server-side.</p>

        <form id="signupForm" enctype="multipart/form-data" autocomplete="off" class="needs-validation" novalidate>
            <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">Full Name</label>
                    <input class="form-control" type="text" name="full_name" required maxlength="120">
                    <div class="invalid-feedback">Please enter full name.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Email</label>
                    <input class="form-control" type="email" name="email" required>
                    <div class="invalid-feedback">Please enter a valid email.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Password</label>
                    <input class="form-control" type="password" name="password" required minlength="8">
                    <div class="invalid-feedback">Password must be at least 8 characters.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Age</label>
                    <input class="form-control" type="number" name="age" min="18" max="80" required>
                    <div class="invalid-feedback">Age must be between 18 and 80.</div>
                </div>

                <div class="col-12"><hr class="my-2"></div>

                <div class="col-12"><h2 class="h6 mb-0">Permanent Address</h2></div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Line 1</label>
                    <input class="form-control" type="text" name="perm_line1" required>
                    <div class="invalid-feedback">Required.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Line 2</label>
                    <input class="form-control" type="text" name="perm_line2">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">City</label>
                    <input class="form-control" type="text" name="perm_city" required>
                    <div class="invalid-feedback">Required.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">State</label>
                    <select class="form-select" name="perm_state" required>
                        <option value="">Select</option>
                        <option>Rajasthan</option>
                        <option>Maharashtra</option>
                        <option>Delhi</option>
                        <option>Uttar Pradesh</option>
                        <option>Gujarat</option>
                    </select>
                    <div class="invalid-feedback">Please select a state.</div>
                </div>

                <div class="col-12"><h2 class="h6 mb-0 mt-2">Current Address</h2></div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Line 1</label>
                    <input class="form-control" type="text" name="curr_line1" required>
                    <div class="invalid-feedback">Required.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Line 2</label>
                    <input class="form-control" type="text" name="curr_line2">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">City</label>
                    <input class="form-control" type="text" name="curr_city" required>
                    <div class="invalid-feedback">Required.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">State</label>
                    <select class="form-select" name="curr_state" required>
                        <option value="">Select</option>
                        <option>Rajasthan</option>
                        <option>Maharashtra</option>
                        <option>Delhi</option>
                        <option>Uttar Pradesh</option>
                        <option>Gujarat</option>
                    </select>
                    <div class="invalid-feedback">Please select a state.</div>
                </div>

                <div class="col-12"><hr class="my-2"></div>

                <div class="col-12 col-md-6">
                    <h2 class="h6 mb-2">Qualifications</h2>
                    <div id="qualificationWrap">
                        <div class="dynamic-row">
                            <input class="form-control" type="text" name="qualifications[]" required>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-action="add-qualification">+</button>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <h2 class="h6 mb-2">Experiences</h2>
                    <div id="experienceWrap">
                        <div class="dynamic-row">
                            <input class="form-control" type="text" name="experiences[]" required>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-action="add-experience">+</button>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">Profile Picture</label>
                    <input class="form-control" type="file" name="profile_picture" accept=".jpg,.jpeg,.png,.webp" data-max-size="2097152">
                    <div class="form-text">Allowed: jpg/png/webp. Max size: 2MB.</div>
                </div>

                <div class="col-12">
                    <button class="btn btn-dark w-100" type="submit">Sign Up</button>
                    <div id="signupMessage" class="form-text mt-2"></div>
                </div>
            </div>
        </form>
    </div>
</div>
