<?php use App\Core\Csrf; ?>
<?php $uploadPath = (string) (($config['upload']['profile_web_path'] ?? '/files/profile/')); ?>
<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <h1 class="h4 mb-3">My Profile</h1>

        <form id="profileForm" enctype="multipart/form-data" class="needs-validation" novalidate>
            <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">

            <div class="row g-3">
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
                <div class="col-12 col-md-6">
                    <label class="form-label">Profile Picture</label>
                    <input class="form-control" type="file" name="profile_picture" accept=".jpg,.jpeg,.png,.webp" data-max-size="2097152">
                    <div class="form-text">Allowed: jpg/png/webp. Max size: 2MB.</div>
                    <?php if (!empty($user['profile_picture'])): ?>
                        <div class="mt-2">
                            <img src="<?= htmlspecialchars($base . $uploadPath . rawurlencode($user['profile_picture']), ENT_QUOTES, 'UTF-8') ?>" alt="Profile" class="rounded border object-fit-cover" style="width:110px;height:110px;">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-12"><hr class="my-2"></div>

                <div class="col-12"><h2 class="h6 mb-0">Permanent Address</h2></div>
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

                <div class="col-12"><h2 class="h6 mb-0 mt-2">Current Address</h2></div>
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
                    <h2 class="h6 mb-2">Qualifications</h2>
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
                    <h2 class="h6 mb-2">Experiences</h2>
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
                    <button class="btn btn-dark w-100" type="submit">Save Changes (AJAX)</button>
                    <div id="profileMessage" class="form-text mt-2"></div>
                </div>
            </div>
        </form>
    </div>
</div>
