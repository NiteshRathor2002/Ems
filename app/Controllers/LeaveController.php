<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\LeaveRequest;

class LeaveController extends Controller
{
    public function index(): void
    {
        $base = (string) ($this->config['base_path'] ?? '/Ems/public');
        if (!Auth::check()) {
            Response::redirect($base . '/login');
        }
        if (Auth::isAdmin()) {
            Response::redirect($base . '/admin/dashboard');
        }

        $userId = (int) Auth::userId();
        $model = new LeaveRequest($this->config);
        if (!$model->featureReady()) {
            Session::flash('error', "Database update required: import 'leave_requests' table from database/schema.sql.");
            $requests = [];
        } else {
            $requests = $model->forUser($userId);
        }

        $this->render('leave/index', [
            'title' => 'Leave',
            'requests' => $requests,
            'today' => date('Y-m-d'),
        ], 'employee');
    }

    public function apply(): void
    {
        $base = (string) ($this->config['base_path'] ?? '/Ems/public');
        if (!Auth::check()) {
            Response::redirect($base . '/login');
        }
        if (Auth::isAdmin()) {
            Response::redirect($base . '/admin/dashboard');
        }
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            Session::flash('error', 'Invalid CSRF token.');
            Response::redirect($base . '/leave');
        }

        $startDate = trim((string) ($_POST['start_date'] ?? ''));
        $endDate = trim((string) ($_POST['end_date'] ?? ''));
        $reason = trim((string) ($_POST['reason'] ?? ''));

        $startDate = $this->normalizeDate($startDate);
        $endDate = $this->normalizeDate($endDate);

        if ($startDate === null || $endDate === null) {
            Session::flash('error', 'Please select valid leave dates.');
            Response::redirect($base . '/leave');
        }

        $today = date('Y-m-d');
        if ($startDate < $today) {
            Session::flash('error', 'You can apply leave only for today or future dates.');
            Response::redirect($base . '/leave');
        }
        if ($endDate < $startDate) {
            Session::flash('error', 'End date must be same as or after start date.');
            Response::redirect($base . '/leave');
        }
        if (!Validator::required($reason) || !Validator::safeText($reason, 500)) {
            Session::flash('error', 'Reason is required (max 500 characters).');
            Response::redirect($base . '/leave');
        }

        $userId = (int) Auth::userId();
        $model = new LeaveRequest($this->config);
        if (!$model->featureReady()) {
            Session::flash('error', "Database update required: import 'leave_requests' table from database/schema.sql.");
            Response::redirect($base . '/leave');
        }
        if ($model->overlapExists($userId, $startDate, $endDate)) {
            Session::flash('error', 'You already have a pending/approved leave request for these dates.');
            Response::redirect($base . '/leave');
        }

        $model->create($userId, $startDate, $endDate, $reason);
        Session::flash('success', 'Leave applied successfully. Waiting for admin approval.');
        Response::redirect($base . '/leave');
    }

    private function normalizeDate(string $value): ?string
    {
        if ($value === '') {
            return null;
        }
        $dt = \DateTime::createFromFormat('Y-m-d', $value);
        if (!$dt) {
            return null;
        }
        $out = $dt->format('Y-m-d');
        return $out === $value ? $out : null;
    }
}
