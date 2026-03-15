<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\User;

class EmployeeController extends Controller
{
    private function allowedStates(): array
    {
        return ['Rajasthan', 'Maharashtra', 'Delhi', 'Uttar Pradesh', 'Gujarat'];
    }

    public function index(): void
    {
        $base = (string) ($this->config['base_path'] ?? '/Ems/public');
        if (!Auth::check() || !Auth::isAdmin()) {
            Response::redirect($base . '/admin/login');
        }

        $q = (string) ($_GET['q'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 5;
        $userModel = new User($this->config);

        $totalEmployees = $userModel->countEmployees();
        $newEmployees7d = $userModel->countEmployeesSince(date('Y-m-d H:i:s', strtotime('-7 days')));

        $filteredTotal = $userModel->countEmployeesMatching($q);
        $totalPages = max(1, (int) ceil($filteredTotal / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $perPage;
        $employees = $userModel->searchEmployeesPage($q, $perPage, $offset);

        $this->render('admin/employees', [
            'title' => 'Employee',
            'employees' => $employees,
            'q' => $q,
            'totalEmployees' => $totalEmployees,
            'newEmployees' => $newEmployees7d,
            'filteredTotal' => $filteredTotal,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => $totalPages,
        ], 'admin');
    }

    public function create(): void
    {
        $base = (string) ($this->config['base_path'] ?? '/Ems/public');
        if (!Auth::check() || !Auth::isAdmin()) {
            Response::redirect($base . '/admin/login');
        }

        $this->render('admin/employee_form', [
            'title' => 'Add Employee',
            'mode' => 'create',
            'employee' => null,
        ], 'admin');
    }

    public function store(): void
    {
        $base = (string) ($this->config['base_path'] ?? '/Ems/public');
        if (!Auth::check() || !Auth::isAdmin()) {
            Response::redirect($base . '/admin/login');
        }
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            Session::flash('error', 'Invalid CSRF token.');
            Response::redirect($base . '/admin/employees/create');
        }

        $userModel = new User($this->config);
        if (!$userModel->departmentFeatureReady()) {
            Session::flash('error', "Database update required: add 'department' column to users table.");
            Response::redirect($base . '/admin/employees/create');
        }

        $fullName = trim($_POST['full_name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $age = $_POST['age'] ?? null;
        $department = trim($_POST['department'] ?? '');

        $payload = [
            'full_name' => $fullName,
            'email' => $email,
            'password_hash' => '',
            'age' => (int) $age,
            'department' => $department !== '' ? $department : null,
            'perm_line1' => trim($_POST['perm_line1'] ?? ''),
            'perm_line2' => trim($_POST['perm_line2'] ?? ''),
            'perm_city' => trim($_POST['perm_city'] ?? ''),
            'perm_state' => trim($_POST['perm_state'] ?? ''),
            'curr_line1' => trim($_POST['curr_line1'] ?? ''),
            'curr_line2' => trim($_POST['curr_line2'] ?? ''),
            'curr_city' => trim($_POST['curr_city'] ?? ''),
            'curr_state' => trim($_POST['curr_state'] ?? ''),
            'profile_picture' => null,
        ];

        if (!Validator::required($fullName) || !Validator::safeText($fullName, 120)) {
            Session::flash('error', 'Valid full name is required.');
            Response::redirect($base . '/admin/employees/create');
        }
        if (!Validator::email($email)) {
            Session::flash('error', 'Valid email is required.');
            Response::redirect($base . '/admin/employees/create');
        }
        if (!Validator::safeText($email, 190)) {
            Session::flash('error', 'Email is too long.');
            Response::redirect($base . '/admin/employees/create');
        }
        if (!Validator::minLength($password, 8)) {
            Session::flash('error', 'Password must be at least 8 characters.');
            Response::redirect($base . '/admin/employees/create');
        }
        if (!Validator::integerBetween($age, 18, 80)) {
            Session::flash('error', 'Age must be between 18 and 80.');
            Response::redirect($base . '/admin/employees/create');
        }
        if (!Validator::required($department) || !Validator::safeText($department, 120)) {
            Session::flash('error', 'Valid department is required.');
            Response::redirect($base . '/admin/employees/create');
        }
        foreach (['perm_line1','perm_city','perm_state','curr_line1','curr_city','curr_state'] as $requiredKey) {
            if (!Validator::required((string) $payload[$requiredKey])) {
                Session::flash('error', 'Please fill all required address fields.');
                Response::redirect($base . '/admin/employees/create');
            }
        }

        foreach ([
            'perm_line1' => 150,
            'perm_line2' => 150,
            'perm_city' => 100,
            'perm_state' => 80,
            'curr_line1' => 150,
            'curr_line2' => 150,
            'curr_city' => 100,
            'curr_state' => 80,
        ] as $key => $max) {
            if (!Validator::safeText((string) ($payload[$key] ?? ''), $max)) {
                Session::flash('error', 'Address field is too long.');
                Response::redirect($base . '/admin/employees/create');
            }
        }

        $states = $this->allowedStates();
        if (!in_array($payload['perm_state'], $states, true) || !in_array($payload['curr_state'], $states, true)) {
            Session::flash('error', 'Please select a valid state.');
            Response::redirect($base . '/admin/employees/create');
        }

        if ($userModel->emailExists($email)) {
            Session::flash('error', 'Email already exists.');
            Response::redirect($base . '/admin/employees/create');
        }

        $payload['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        $payload['profile_picture'] = $this->handleProfileUpload(null);

        $employeeId = $userModel->createEmployee($payload);
        Session::flash('success', 'Employee created (ID: ' . $employeeId . ').');
        Response::redirect($base . '/admin/employees');
    }

    public function show(string $id): void
    {
        $base = (string) ($this->config['base_path'] ?? '/Ems/public');
        if (!Auth::check() || !Auth::isAdmin()) {
            Response::redirect($base . '/admin/login');
        }

        $employeeId = (int) $id;
        $employee = (new User($this->config))->findEmployeeById($employeeId);
        if (!$employee) {
            Session::flash('error', 'Employee not found.');
            Response::redirect($base . '/admin/employees');
        }

        $this->render('admin/employee_show', [
            'title' => 'Employee Details',
            'employee' => $employee,
        ], 'admin');
    }

    public function edit(string $id): void
    {
        $base = (string) ($this->config['base_path'] ?? '/Ems/public');
        if (!Auth::check() || !Auth::isAdmin()) {
            Response::redirect($base . '/admin/login');
        }

        $employeeId = (int) $id;
        $employee = (new User($this->config))->findEmployeeById($employeeId);
        if (!$employee) {
            Session::flash('error', 'Employee not found.');
            Response::redirect($base . '/admin/employees');
        }

        $this->render('admin/employee_form', [
            'title' => 'Edit Employee',
            'mode' => 'edit',
            'employee' => $employee,
        ], 'admin');
    }

    public function update(string $id): void
    {
        $base = (string) ($this->config['base_path'] ?? '/Ems/public');
        if (!Auth::check() || !Auth::isAdmin()) {
            Response::redirect($base . '/admin/login');
        }
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            Session::flash('error', 'Invalid CSRF token.');
            Response::redirect($base . '/admin/employees');
        }

        $employeeId = (int) $id;
        $userModel = new User($this->config);
        if (!$userModel->departmentFeatureReady()) {
            Session::flash('error', "Database update required: add 'department' column to users table.");
            Response::redirect($base . '/admin/employees/edit/' . $employeeId);
        }
        $existing = $userModel->findEmployeeById($employeeId);
        if (!$existing) {
            Session::flash('error', 'Employee not found.');
            Response::redirect($base . '/admin/employees');
        }

        $fullName = trim($_POST['full_name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $age = $_POST['age'] ?? null;
        $department = trim($_POST['department'] ?? '');

        if (!Validator::required($fullName) || !Validator::safeText($fullName, 120)) {
            Session::flash('error', 'Valid full name is required.');
            Response::redirect($base . '/admin/employees/edit/' . $employeeId);
        }
        if (!Validator::email($email)) {
            Session::flash('error', 'Valid email is required.');
            Response::redirect($base . '/admin/employees/edit/' . $employeeId);
        }
        if (!Validator::safeText($email, 190)) {
            Session::flash('error', 'Email is too long.');
            Response::redirect($base . '/admin/employees/edit/' . $employeeId);
        }
        if (!Validator::integerBetween($age, 18, 80)) {
            Session::flash('error', 'Age must be between 18 and 80.');
            Response::redirect($base . '/admin/employees/edit/' . $employeeId);
        }
        if (!Validator::required($department) || !Validator::safeText($department, 120)) {
            Session::flash('error', 'Valid department is required.');
            Response::redirect($base . '/admin/employees/edit/' . $employeeId);
        }

        if ($userModel->emailExists($email, $employeeId)) {
            Session::flash('error', 'Email already exists.');
            Response::redirect($base . '/admin/employees/edit/' . $employeeId);
        }

        $payload = [
            'full_name' => $fullName,
            'email' => $email,
            'password_hash' => $existing['password_hash'],
            'age' => (int) $age,
            'department' => $department !== '' ? $department : null,
            'perm_line1' => trim($_POST['perm_line1'] ?? ''),
            'perm_line2' => trim($_POST['perm_line2'] ?? ''),
            'perm_city' => trim($_POST['perm_city'] ?? ''),
            'perm_state' => trim($_POST['perm_state'] ?? ''),
            'curr_line1' => trim($_POST['curr_line1'] ?? ''),
            'curr_line2' => trim($_POST['curr_line2'] ?? ''),
            'curr_city' => trim($_POST['curr_city'] ?? ''),
            'curr_state' => trim($_POST['curr_state'] ?? ''),
            'profile_picture' => $existing['profile_picture'],
        ];

        foreach (['perm_line1','perm_city','perm_state','curr_line1','curr_city','curr_state'] as $requiredKey) {
            if (!Validator::required((string) $payload[$requiredKey])) {
                Session::flash('error', 'Please fill all required address fields.');
                Response::redirect($base . '/admin/employees/edit/' . $employeeId);
            }
        }

        foreach ([
            'perm_line1' => 150,
            'perm_line2' => 150,
            'perm_city' => 100,
            'perm_state' => 80,
            'curr_line1' => 150,
            'curr_line2' => 150,
            'curr_city' => 100,
            'curr_state' => 80,
        ] as $key => $max) {
            if (!Validator::safeText((string) ($payload[$key] ?? ''), $max)) {
                Session::flash('error', 'Address field is too long.');
                Response::redirect($base . '/admin/employees/edit/' . $employeeId);
            }
        }

        $states = $this->allowedStates();
        if (!in_array($payload['perm_state'], $states, true) || !in_array($payload['curr_state'], $states, true)) {
            Session::flash('error', 'Please select a valid state.');
            Response::redirect($base . '/admin/employees/edit/' . $employeeId);
        }

        if ($password !== '') {
            if (!Validator::minLength($password, 8)) {
                Session::flash('error', 'Password must be at least 8 characters (or leave blank).');
                Response::redirect($base . '/admin/employees/edit/' . $employeeId);
            }
            $payload['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $payload['profile_picture'] = $this->handleProfileUpload($existing['profile_picture']);
        $userModel->updateEmployee($employeeId, $payload);

        Session::flash('success', 'Employee updated.');
        Response::redirect($base . '/admin/employees/show/' . $employeeId);
    }

    public function destroy(string $id): void
    {
        $base = (string) ($this->config['base_path'] ?? '/Ems/public');
        if (!Auth::check() || !Auth::isAdmin()) {
            Response::redirect($base . '/admin/login');
        }
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            Session::flash('error', 'Invalid CSRF token.');
            Response::redirect($base . '/admin/employees');
        }

        $employeeId = (int) $id;
        $userModel = new User($this->config);
        $employee = $userModel->findEmployeeById($employeeId);
        if (!$employee) {
            Session::flash('error', 'Employee not found.');
            Response::redirect($base . '/admin/employees');
        }

        if (!empty($employee['profile_picture'])) {
            $this->deleteProfilePicture((string) $employee['profile_picture']);
        }

        $userModel->deleteEmployee($employeeId);
        Session::flash('success', 'Employee deleted.');
        Response::redirect($base . '/admin/employees');
    }

    private function handleProfileUpload(?string $currentFile): ?string
    {
        if (!isset($_FILES['profile_picture']) || ($_FILES['profile_picture']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $currentFile;
        }

        $file = $_FILES['profile_picture'];
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Profile picture upload failed.');
            Response::redirect((string) ($this->config['base_path'] ?? '/Ems/public') . '/admin/employees');
        }

        $upload = $this->config['upload'];
        if (($file['size'] ?? 0) > $upload['max_size']) {
            Session::flash('error', 'Image size must be <= 2MB.');
            Response::redirect((string) ($this->config['base_path'] ?? '/Ems/public') . '/admin/employees');
        }

        $tmp = $file['tmp_name'];
        $mime = mime_content_type($tmp) ?: '';
        $ext = $upload['allowed_mime'][$mime] ?? null;
        if ($ext === null) {
            Session::flash('error', 'Only jpg, png, webp are allowed.');
            Response::redirect((string) ($this->config['base_path'] ?? '/Ems/public') . '/admin/employees');
        }

        $safeName = 'profile_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $targetDir = $upload['profile_dir'];
        if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
            Session::flash('error', 'Unable to create upload directory.');
            Response::redirect((string) ($this->config['base_path'] ?? '/Ems/public') . '/admin/employees');
        }

        $targetPath = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $safeName;
        if (!move_uploaded_file($tmp, $targetPath)) {
            Session::flash('error', 'Unable to store uploaded image.');
            Response::redirect((string) ($this->config['base_path'] ?? '/Ems/public') . '/admin/employees');
        }

        if ($currentFile) {
            $this->deleteProfilePicture($currentFile);
        }

        return $safeName;
    }

    private function deleteProfilePicture(string $file): void
    {
        $dir = (string) ($this->config['upload']['profile_dir'] ?? '');
        if ($dir === '') {
            return;
        }
        $old = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . basename($file);
        if (is_file($old)) {
            @unlink($old);
        }
    }
}
