<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Response;
use App\Core\Session;
use App\Models\LeaveRequest;

class LeaveController extends Controller
{
    public function index(): void
    {
        $base = (string) ($this->config['base_path'] ?? '/Ems/public');
        if (!Auth::check() || !Auth::isAdmin()) {
            Response::redirect($base . '/admin/login');
        }

        $status = trim((string) ($_GET['status'] ?? 'pending'));
        $allowed = ['pending', 'approved', 'rejected', 'all'];
        if (!in_array($status, $allowed, true)) {
            $status = 'pending';
        }

        $statusFilter = $status === 'all' ? null : $status;
        $model = new LeaveRequest($this->config);
        if (!$model->featureReady()) {
            Session::flash('error', "Database update required: import 'leave_requests' table from database/schema.sql.");
            $requests = [];
        } else {
            $requests = $model->listForAdmin($statusFilter);
        }

        $this->render('admin/leaves', [
            'title' => 'Leave Requests',
            'requests' => $requests,
            'status' => $status,
        ], 'admin');
    }

    public function approve(string $id): void
    {
        $this->decide($id, 'approved');
    }

    public function reject(string $id): void
    {
        $this->decide($id, 'rejected');
    }

    private function decide(string $id, string $decision): void
    {
        $base = (string) ($this->config['base_path'] ?? '/Ems/public');
        if (!Auth::check() || !Auth::isAdmin()) {
            Response::redirect($base . '/admin/login');
        }
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            Session::flash('error', 'Invalid CSRF token.');
            Response::redirect($base . '/admin/leaves');
        }

        $status = trim((string) ($_POST['status'] ?? 'pending'));
        $allowed = ['pending', 'approved', 'rejected', 'all'];
        if (!in_array($status, $allowed, true)) {
            $status = 'pending';
        }
        $returnUrl = $base . '/admin/leaves?status=' . urlencode($status);

        $leaveId = (int) $id;
        if ($leaveId <= 0) {
            Session::flash('error', 'Invalid leave request.');
            Response::redirect($returnUrl);
        }

        $adminId = (int) (Auth::userId() ?? 0);
        if ($adminId <= 0) {
            Session::flash('error', 'Unauthorized.');
            Response::redirect($base . '/admin/login');
        }

        $model = new LeaveRequest($this->config);
        if (!$model->featureReady()) {
            Session::flash('error', "Database update required: import 'leave_requests' table from database/schema.sql.");
            Response::redirect($returnUrl);
        }
        $ok = $model->decide($leaveId, $adminId, $decision);
        if ($ok) {
            Session::flash('success', 'Leave request ' . $decision . '.');
            Response::redirect($returnUrl);
        }

        $existing = $model->findById($leaveId);
        if (!$existing) {
            Session::flash('error', 'Leave request not found.');
            Response::redirect($returnUrl);
        }
        if ((string) ($existing['status'] ?? '') !== 'pending') {
            Session::flash('error', 'This leave request is already processed.');
            Response::redirect($returnUrl);
        }

        Session::flash('error', 'Unable to process leave request.');
        Response::redirect($returnUrl);
    }
}
