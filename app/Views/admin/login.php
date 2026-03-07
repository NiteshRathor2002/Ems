<?php use App\Core\Csrf; ?>
<section class="card small">
    <h1>Admin Login</h1>
    <form id="adminLoginForm" autocomplete="off">
        <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
        <label>Email
            <input type="email" name="email" required>
        </label>
        <label>Password
            <input type="password" name="password" required>
        </label>
        <button type="submit">Login as Admin</button>
        <p id="adminLoginMessage" class="message"></p>
    </form>
</section>
