<?php use App\Core\Csrf; ?>
<?php
$uploadPath = (string) (($config['upload']['profile_web_path'] ?? '/files/profile/'));
$userInitials = strtoupper(mb_substr(trim((string) $user['full_name']), 0, 1)) ?: 'E';
$profileUrl = '';
if (!empty($user['profile_picture'])) {
    $profileUrl = $base . $uploadPath . rawurlencode((string) $user['profile_picture']);
}
$department = trim((string) ($user['department'] ?? ''));
$location = trim((string) ($user['curr_city'] ?? ''));
if ($location !== '' && !empty($user['curr_state'])) {
    $location .= ', ' . $user['curr_state'];
} elseif ($location === '' && !empty($user['curr_state'])) {
    $location = (string) $user['curr_state'];
}
?>
<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <h1 class="h4 mb-3">My Profile</h1>

        <form id="profileForm" enctype="multipart/form-data" class="needs-validation" novalidate>
            <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">

            <div class="row g-3">
                <div class="col-12">
                    <div class="profile-hero">
                        <div class="profile-hero-body">
                            <div class="profile-photo">
                                <button type="button" class="profile-photo-btn" id="profilePhotoBtn" aria-label="Change profile picture">
                                    <img id="profilePhotoPreview" class="profile-photo-img<?= $profileUrl ? '' : ' d-none' ?>" src="<?= htmlspecialchars($profileUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Profile picture">
                                    <span id="profilePhotoFallback" class="profile-photo-fallback<?= $profileUrl ? ' d-none' : '' ?>"><?= htmlspecialchars($userInitials, ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="profile-photo-icon" aria-hidden="true"><i class="bi bi-camera"></i></span>
                                </button>
                                <input class="visually-hidden" type="file" id="profilePictureInput" name="profile_picture" accept=".jpg,.jpeg,.png,.webp" data-max-size="2097152">
                                <div class="profile-photo-hint">Click the camera to update your photo.</div>
                                <div class="form-text">Allowed: jpg/png/webp. Max size: 2MB.</div>
                            </div>
                            <div class="profile-meta">
                                <div class="profile-title">Welcome, <?= htmlspecialchars((string) $user['full_name'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="profile-sub">Employee Dashboard</div>
                                <div class="profile-chips">
                                    <?php if ($department !== ''): ?>
                                        <span class="profile-chip"><i class="bi bi-building"></i><?= htmlspecialchars($department, ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                    <span class="profile-chip"><i class="bi bi-geo-alt"></i><?= htmlspecialchars($location !== '' ? $location : 'Location not set', ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="profile-chip"><i class="bi bi-person"></i><?= (int) $user['age'] ?> yrs</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <h2 class="section-title">Personal Details</h2>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Full Name</label>
                    <input class="form-control" type="text" name="full_name" value="<?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Email (not editable)</label>
                    <input class="form-control" type="email" value="<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>" disabled>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Age</label>
                    <input class="form-control" type="number" name="age" min="18" max="80" value="<?= (int) $user['age'] ?>" required>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Department (not editable)</label>
                    <input class="form-control" type="text" value="<?= htmlspecialchars((string) ($user['department'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" disabled>
                </div>

                <div class="col-12"><hr class="my-2"></div>

                <div class="col-12"><h2 class="section-title">Permanent Address</h2></div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Line 1</label>
                    <input class="form-control" type="text" name="perm_line1" value="<?= htmlspecialchars($user['perm_line1'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Line 2</label>
                    <input class="form-control" type="text" name="perm_line2" value="<?= htmlspecialchars($user['perm_line2'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">City</label>
                    <input class="form-control" type="text" name="perm_city" value="<?= htmlspecialchars($user['perm_city'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">State</label>
                    <?php $states = ['Rajasthan','Maharashtra','Delhi','Uttar Pradesh','Gujarat']; ?>
                    <select class="form-select" name="perm_state" required>
                        <?php foreach ($states as $state): ?>
                            <option value="<?= htmlspecialchars($state, ENT_QUOTES, 'UTF-8') ?>" <?= $user['perm_state'] === $state ? 'selected' : '' ?>><?= htmlspecialchars($state, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12"><h2 class="section-title mt-2">Current Address</h2></div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Line 1</label>
                    <input class="form-control" type="text" name="curr_line1" value="<?= htmlspecialchars($user['curr_line1'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Line 2</label>
                    <input class="form-control" type="text" name="curr_line2" value="<?= htmlspecialchars($user['curr_line2'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">City</label>
                    <input class="form-control" type="text" name="curr_city" value="<?= htmlspecialchars($user['curr_city'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">State</label>
                    <select class="form-select" name="curr_state" required>
                        <?php foreach ($states as $state): ?>
                            <option value="<?= htmlspecialchars($state, ENT_QUOTES, 'UTF-8') ?>" <?= $user['curr_state'] === $state ? 'selected' : '' ?>><?= htmlspecialchars($state, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12"><hr class="my-2"></div>

                <div class="col-12 col-md-6">
                    <h2 class="section-title mb-2">Qualifications</h2>
                    <div id="qualificationWrap">
                        <?php foreach ($qualifications as $index => $q): ?>
                            <div class="dynamic-row">
                                <input class="form-control" type="text" name="qualifications[]" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>" required>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-action="<?= $index === 0 ? 'add-qualification' : 'remove-row' ?>"><?= $index === 0 ? '+' : '-' ?></button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <h2 class="section-title mb-2">Experiences</h2>
                    <div id="experienceWrap">
                        <?php foreach ($experiences as $index => $e): ?>
                            <div class="dynamic-row">
                                <input class="form-control" type="text" name="experiences[]" value="<?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?>" required>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-action="<?= $index === 0 ? 'add-experience' : 'remove-row' ?>"><?= $index === 0 ? '+' : '-' ?></button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="col-12">
                    <button class="btn btn-dark w-100" type="submit">Save Changes</button>
                    <div id="profileMessage" class="form-text mt-2"></div>
                </div>
            </div>
        </form>
    </div>
</div>
