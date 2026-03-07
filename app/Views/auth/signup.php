<?php use App\Core\Csrf; ?>
<section class="card">
    <h1>Create Account</h1>
    <p class="muted">Register as an employee. All fields are validated server-side and client-side.</p>

    <form id="signupForm" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">

        <div class="grid two">
            <label>Full Name
                <input type="text" name="full_name" required maxlength="120">
            </label>
            <label>Email
                <input type="email" name="email" required>
            </label>
            <label>Password
                <input type="password" name="password" required minlength="8">
            </label>
            <label>Age
                <input type="number" name="age" min="18" max="80" required>
            </label>
        </div>

        <h3>Permanent Address</h3>
        <div class="grid two">
            <label>Line 1<input type="text" name="perm_line1" required></label>
            <label>Line 2<input type="text" name="perm_line2"></label>
            <label>City<input type="text" name="perm_city" required></label>
            <label>State
                <select name="perm_state" required>
                    <option value="">Select</option>
                    <option>Rajasthan</option>
                    <option>Maharashtra</option>
                    <option>Delhi</option>
                    <option>Uttar Pradesh</option>
                    <option>Gujarat</option>
                </select>
            </label>
        </div>

        <h3>Current Address</h3>
        <div class="grid two">
            <label>Line 1<input type="text" name="curr_line1" required></label>
            <label>Line 2<input type="text" name="curr_line2"></label>
            <label>City<input type="text" name="curr_city" required></label>
            <label>State
                <select name="curr_state" required>
                    <option value="">Select</option>
                    <option>Rajasthan</option>
                    <option>Maharashtra</option>
                    <option>Delhi</option>
                    <option>Uttar Pradesh</option>
                    <option>Gujarat</option>
                </select>
            </label>
        </div>

        <h3>Qualifications</h3>
        <div id="qualificationWrap">
            <div class="dynamic-row">
                <input type="text" name="qualifications[]" required>
                <button type="button" class="btn-secondary" data-action="add-qualification">+</button>
            </div>
        </div>

        <h3>Experiences</h3>
        <div id="experienceWrap">
            <div class="dynamic-row">
                <input type="text" name="experiences[]" required>
                <button type="button" class="btn-secondary" data-action="add-experience">+</button>
            </div>
        </div>

        <label>Profile Picture
            <input type="file" name="profile_picture" accept=".jpg,.jpeg,.png,.webp">
        </label>

        <button type="submit">Sign Up</button>
        <p id="signupMessage" class="message"></p>
    </form>
</section>
