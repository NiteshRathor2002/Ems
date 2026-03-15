<?php

use App\Core\Csrf;

$requests = is_array($requests ?? null) ? $requests : [];
$status = (string) ($status ?? 'pending');
$baseUrl = htmlspecialchars($base, ENT_QUOTES, 'UTF-8') . '/admin/leaves';

$badgeFor = static function (string $s): string {
    return match ($s) {
        'approved' => 'success',
        'rejected' => 'danger',
        default => 'warning',
    };
};

$linkFor = static function (string $s) use ($baseUrl): string {
    return $baseUrl . '?' . htmlspecialchars(http_build_query(['status' => $s]), ENT_QUOTES, 'UTF-8');
};
?>

<div class="card shadow-sm border-0 mb-3">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <div class="text-muted small">Home / Leave</div>
            <h1 class="h5 mb-0">Leave Requests</h1>
        </div>
        <div class="btn-group" role="group" aria-label="Leave filter">
            <a class="btn btn-sm <?= $status === 'pending' ? 'btn-primary' : 'btn-outline-primary' ?>" href="<?= $linkFor('pending') ?>">Pending</a>
            <a class="btn btn-sm <?= $status === 'approved' ? 'btn-primary' : 'btn-outline-primary' ?>" href="<?= $linkFor('approved') ?>">Approved</a>
            <a class="btn btn-sm <?= $status === 'rejected' ? 'btn-primary' : 'btn-outline-primary' ?>" href="<?= $linkFor('rejected') ?>">Rejected</a>
            <a class="btn btn-sm <?= $status === 'all' ? 'btn-primary' : 'btn-outline-primary' ?>" href="<?= $linkFor('all') ?>">All</a>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th style="width: 220px;">Employee</th>
                        <th style="width: 170px;">Dates</th>
                        <th>Reason</th>
                        <th style="width: 120px;">Status</th>
                        <th style="width: 170px;">Applied</th>
                        <th class="text-end" style="width: 220px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($requests)): ?>
                        <tr><td colspan="7" class="text-muted">No leave requests found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($requests as $r): ?>
                            <?php
                            $id = (int) ($r['id'] ?? 0);
                            $s = (string) ($r['status'] ?? 'pending');
                            $dates = (string) ($r['start_date'] ?? '');
                            if (!empty($r['end_date']) && (string) $r['end_date'] !== (string) $r['start_date']) {
                                $dates .= ' to ' . (string) $r['end_date'];
                            }
                            $isPending = $s === 'pending';
                            ?>
                            <tr>
                                <td><?= $id ?></td>
                                <td>
                                    <div class="fw-semibold"><?= htmlspecialchars((string) ($r['employee_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                                    <div class="text-muted small"><?= htmlspecialchars((string) ($r['employee_email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                                </td>
                                <td><?= htmlspecialchars($dates, ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= nl2br(htmlspecialchars((string) ($r['reason'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></td>
                                <td>
                                    <span class="badge text-bg-<?= htmlspecialchars($badgeFor($s), ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars(ucfirst($s), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="text-muted small"><?= htmlspecialchars((string) ($r['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="text-end">
                                    <?php if ($isPending): ?>
                                        <div class="d-inline-flex flex-wrap gap-1 justify-content-end">
                                            <form method="post" action="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/admin/leaves/approve/<?= $id ?>" class="m-0" onsubmit="return confirm('Approve this leave request?');">
                                                <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                                                <input type="hidden" name="status" value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>">
                                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                            </form>
                                            <form method="post" action="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/admin/leaves/reject/<?= $id ?>" class="m-0" onsubmit="return confirm('Reject this leave request?');">
                                                <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                                                <input type="hidden" name="status" value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">
                                            <?= !empty($r['decided_at']) ? 'Processed: ' . htmlspecialchars((string) $r['decided_at'], ENT_QUOTES, 'UTF-8') : 'Processed' ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

