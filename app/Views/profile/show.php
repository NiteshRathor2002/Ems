<?php use App\Core\Csrf; ?>
<section class="card">
    <h1>My Profile</h1>

    <form id="profileForm" enctype="multipart/form-data">
        <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">

        <div class="grid two">
            <label>Full Name
                <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?>" required>
            </label>
            <label>Email (not editable)
                <input type="email" value="<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>" disabled>
            </label>
            <label>Age
                <input type="number" name="age" min="18" max="80" value="<?= (int) $user['age'] ?>" required>
            </label>
            <label>Profile Picture
                <input type="file" name="profile_picture" accept=".jpg,.jpeg,.png,.webp">
            </label>
        </div>

        <?php if (!empty($user['profile_picture'])): ?>
            <img src="/Ems/public/uploads/profiles/<?= rawurlencode($user['profile_picture']) ?>" alt="Profile" class="avatar">
        <?php endif; ?>

        <h3>Permanent Address</h3>
        <div class="grid two">
            <label>Line 1<input type="text" name="perm_line1" value="<?= htmlspecialchars($user['perm_line1'], ENT_QUOTES, 'UTF-8') ?>" required></label>
            <label>Line 2<input type="text" name="perm_line2" value="<?= htmlspecialchars($user['perm_line2'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></label>
            <label>City<input type="text" name="perm_city" value="<?= htmlspecialchars($user['perm_city'], ENT_QUOTES, 'UTF-8') ?>" required></label>
            <label>State
                <select name="perm_state" required>
                    <?php $states = ['Rajasthan','Maharashtra','Delhi','Uttar Pradesh','Gujarat']; foreach ($states as $state): ?>
                        <option value="<?= $state ?>" <?= $user['perm_state'] === $state ? 'selected' : '' ?>><?= $state ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>

        <h3>Current Address</h3>
        <div class="grid two">
            <label>Line 1<input type="text" name="curr_line1" value="<?= htmlspecialchars($user['curr_line1'], ENT_QUOTES, 'UTF-8') ?>" required></label>
            <label>Line 2<input type="text" name="curr_line2" value="<?= htmlspecialchars($user['curr_line2'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></label>
            <label>City<input type="text" name="curr_city" value="<?= htmlspecialchars($user['curr_city'], ENT_QUOTES, 'UTF-8') ?>" required></label>
            <label>State
                <select name="curr_state" required>
                    <?php foreach ($states as $state): ?>
                        <option value="<?= $state ?>" <?= $user['curr_state'] === $state ? 'selected' : '' ?>><?= $state ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>

        <h3>Qualifications</h3>
        <div id="qualificationWrap">
            <?php foreach ($qualifications as $index => $q): ?>
                <div class="dynamic-row">
                    <input type="text" name="qualifications[]" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>" required>
                    <button type="button" class="btn-secondary" data-action="<?= $index === 0 ? 'add-qualification' : 'remove-row' ?>"><?= $index === 0 ? '+' : '-' ?></button>
                </div>
            <?php endforeach; ?>
        </div>

        <h3>Experiences</h3>
        <div id="experienceWrap">
            <?php foreach ($experiences as $index => $e): ?>
                <div class="dynamic-row">
                    <input type="text" name="experiences[]" value="<?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?>" required>
                    <button type="button" class="btn-secondary" data-action="<?= $index === 0 ? 'add-experience' : 'remove-row' ?>"><?= $index === 0 ? '+' : '-' ?></button>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="submit">Save Changes (AJAX)</button>
        <p id="profileMessage" class="message"></p>
    </form>
</section>
