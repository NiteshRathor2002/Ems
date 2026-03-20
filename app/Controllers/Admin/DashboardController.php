<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Response;
use App\Models\LeaveRequest;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(): void
    {
        $base = (string) ($this->config['base_path'] ?? '/Ems/public');
        if (!Auth::check() || !Auth::isAdmin()) {
            Response::redirect($base . '/admin/login');
        }

        $userModel = new User($this->config);
        $totalEmployees = $userModel->countEmployees();
        $newEmployees7d = $userModel->countEmployeesSince(date('Y-m-d H:i:s', strtotime('-7 days')));
        $recent = $userModel->recentEmployees(8);

        $leaveModel = new LeaveRequest($this->config);
        $leaveCounts = $leaveModel->statusCounts();

        $this->render('admin/dashboard', [
            'title' => 'Dashboard',
            'totalEmployees' => $totalEmployees,
            'activeEmployees' => $totalEmployees,
            'newEmployees' => $newEmployees7d,
            'recentEmployees' => $recent,
            'pendingLeaves' => (int) ($leaveCounts['pending'] ?? 0),
            'approvedLeaves' => (int) ($leaveCounts['approved'] ?? 0),
            'rejectedLeaves' => (int) ($leaveCounts['rejected'] ?? 0),
        ], 'admin');
    }
}
