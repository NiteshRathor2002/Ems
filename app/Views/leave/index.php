<?php

use App\Core\Csrf;

$requests = is_array($requests ?? null) ? $requests : [];
$today = (string) ($today ?? date('Y-m-d'));

$badgeFor = static function (string $status): string {
    return match ($status) {
        'approved' => 'success',
        'rejected' => 'danger',
        default => 'warning',
    };
};
?>

<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <div class="text-muted small">Home / Leave</div>
        <h1 class="h5 mb-0">Apply Leave</h1>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form method="post" action="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/leave/apply" class="needs-validation" novalidate>
                    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">

                    <div class="row g-2">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Start date</label>
                            <input type="date" name="start_date" class="form-control" required min="<?= htmlspecialchars($today, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="invalid-feedback">Start date is required.</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">End date</label>
                            <input type="date" name="end_date" class="form-control" required min="<?= htmlspecialchars($today, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="invalid-feedback">End date is required.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Reason</label>
                            <textarea name="reason" class="form-control" rows="4" required maxlength="500" placeholder="Write a short reason..."></textarea>
                            <div class="invalid-feedback">Reason is required.</div>
                            <div class="form-text">Leave can be applied for today and future dates only.</div>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary w-100" type="submit"><i class="bi bi-send me-1"></i>Submit Request</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h2 class="h6 mb-3">My Leave Requests</h2>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">ID</th>
                                <th style="width: 160px;">Dates</th>
                                <th>Reason</th>
                                <th style="width: 120px;">Status</th>
                                <th style="width: 170px;">Applied</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($requests)): ?>
                                <tr><td colspan="5" class="text-muted">No leave requests yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($requests as $r): ?>
                                    <?php
                                    $status = (string) ($r['status'] ?? 'pending');
                                    $dates = (string) ($r['start_date'] ?? '');
                                    if (!empty($r['end_date']) && (string) $r['end_date'] !== (string) $r['start_date']) {
                                        $dates .= ' to ' . (string) $r['end_date'];
                                    }
                                    ?>
                                    <tr>
                                        <td><?= (int) ($r['id'] ?? 0) ?></td>
                                        <td><?= htmlspecialchars($dates, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= nl2br(htmlspecialchars((string) ($r['reason'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></td>
                                        <td>
                                            <span class="badge text-bg-<?= htmlspecialchars($badgeFor($status), ENT_QUOTES, 'UTF-8') ?>">
                                                <?= htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </td>
                                        <td class="text-muted small"><?= htmlspecialchars((string) ($r['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

