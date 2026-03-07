<?php use App\Core\Csrf; ?>
<section class="card small">
    <h1>User Login</h1>
    <form id="loginForm" autocomplete="off">
        <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
        <label>Email
            <input type="email" name="email" required>
        </label>
        <label>Password
            <input type="password" name="password" required>
        </label>
        <button type="submit">Login</button>
        <p id="loginMessage" class="message"></p>
    </form>
</section>
