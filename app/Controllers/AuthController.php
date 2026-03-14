<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Experience;
use App\Models\Qualification;
use App\Models\User;

class AuthController extends Controller
{
    public function signupPage(): void
    {
        if (Auth::check()) {
            Response::redirect((string) ($this->config['base_path'] ?? '/Ems/public') . '/profile');
        }
        $this->render('auth/signup', ['title' => 'Sign Up']);
    }

    public function loginPage(): void
    {
        if (Auth::check()) {
            Response::redirect((string) ($this->config['base_path'] ?? '/Ems/public') . '/profile');
        }
        $this->render('auth/login', ['title' => 'Login']);
    }

    public function signup(): void
    {
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            Response::json(['ok' => false, 'message' => 'Invalid CSRF token'], 419);
        }

        $fullName = trim($_POST['full_name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $age = $_POST['age'] ?? null;

        $qualifications = array_values(array_filter($_POST['qualifications'] ?? [], static fn ($v) => trim((string) $v) !== ''));
        $experiences = array_values(array_filter($_POST['experiences'] ?? [], static fn ($v) => trim((string) $v) !== ''));

        $payload = [
            'perm_line1' => trim($_POST['perm_line1'] ?? ''),
            'perm_line2' => trim($_POST['perm_line2'] ?? ''),
            'perm_city' => trim($_POST['perm_city'] ?? ''),
            'perm_state' => trim($_POST['perm_state'] ?? ''),
            'curr_line1' => trim($_POST['curr_line1'] ?? ''),
            'curr_line2' => trim($_POST['curr_line2'] ?? ''),
            'curr_city' => trim($_POST['curr_city'] ?? ''),
            'curr_state' => trim($_POST['curr_state'] ?? ''),
        ];

        if (!Validator::required($fullName) || !Validator::safeText($fullName, 120)) {
            Response::json(['ok' => false, 'message' => 'Valid full name is required'], 422);
        }
        if (!Validator::email($email)) {
            Response::json(['ok' => false, 'message' => 'Valid email is required'], 422);
        }
        if (!Validator::minLength($password, 8)) {
            Response::json(['ok' => false, 'message' => 'Password must be at least 8 characters'], 422);
        }
        if (!Validator::integerBetween($age, 18, 80)) {
            Response::json(['ok' => false, 'message' => 'Age must be between 18 and 80'], 422);
        }

        foreach ($payload as $key => $value) {
            if (!str_ends_with($key, 'line2') && !Validator::required($value)) {
                Response::json(['ok' => false, 'message' => 'Please fill all required address fields'], 422);
            }
            if (!Validator::safeText($value, 150)) {
                Response::json(['ok' => false, 'message' => 'Address values are too long'], 422);
            }
        }

        if (count($qualifications) === 0 || count($experiences) === 0) {
            Response::json(['ok' => false, 'message' => 'At least one qualification and one experience are required'], 422);
        }

        $userModel = new User($this->config);
        if ($userModel->findByEmail($email)) {
            Response::json(['ok' => false, 'message' => 'Email already registered'], 409);
        }

        $profilePicture = $this->handleProfileUpload();

        $userId = $userModel->create([
            'full_name' => $fullName,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'age' => (int) $age,
            'perm_line1' => $payload['perm_line1'],
            'perm_line2' => $payload['perm_line2'],
            'perm_city' => $payload['perm_city'],
            'perm_state' => $payload['perm_state'],
            'curr_line1' => $payload['curr_line1'],
            'curr_line2' => $payload['curr_line2'],
            'curr_city' => $payload['curr_city'],
            'curr_state' => $payload['curr_state'],
            'profile_picture' => $profilePicture,
        ]);

        (new Qualification($this->config))->saveMany($userId, $qualifications);
        (new Experience($this->config))->saveMany($userId, $experiences);

        Auth::login($userId);
        Session::set('user_name', $fullName);

        Response::json(['ok' => true, 'message' => 'Signup successful', 'redirect' => (string) ($this->config['base_path'] ?? '/Ems/public') . '/profile']);
    }

    public function login(): void
    {
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            Response::json(['ok' => false, 'message' => 'Invalid CSRF token'], 419);
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if (!Validator::email($email) || $password === '') {
            Response::json(['ok' => false, 'message' => 'Email and password are required'], 422);
        }

        $user = (new User($this->config))->findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            Response::json(['ok' => false, 'message' => 'Invalid credentials'], 401);
        }

        Auth::login((int) $user['id'], (bool) $user['is_admin']);
        Session::set('user_name', (string) ($user['full_name'] ?? 'User'));
        Response::json(['ok' => true, 'message' => 'Login successful', 'redirect' => (string) ($this->config['base_path'] ?? '/Ems/public') . '/profile']);
    }

    public function logout(): void
    {
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            Response::redirect((string) ($this->config['base_path'] ?? '/Ems/public') . '/login');
        }
        Auth::logout();
        Response::redirect((string) ($this->config['base_path'] ?? '/Ems/public') . '/login');
    }

    private function handleProfileUpload(?string $currentFile = null): ?string
    {
        if (!isset($_FILES['profile_picture']) || ($_FILES['profile_picture']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $currentFile;
        }

        $file = $_FILES['profile_picture'];
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            Response::json(['ok' => false, 'message' => 'Profile picture upload failed'], 422);
        }

        $upload = $this->config['upload'];
        if (($file['size'] ?? 0) > $upload['max_size']) {
            Response::json(['ok' => false, 'message' => 'Image size must be <= 2MB'], 422);
        }

        $tmp = $file['tmp_name'];
        $mime = mime_content_type($tmp) ?: '';
        $ext = $upload['allowed_mime'][$mime] ?? null;
        if ($ext === null) {
            Response::json(['ok' => false, 'message' => 'Only jpg, png, webp are allowed'], 422);
        }

        $safeName = 'profile_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $targetDir = $upload['profile_dir'];
        if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
            Response::json(['ok' => false, 'message' => 'Unable to create upload directory'], 500);
        }

        $targetPath = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $safeName;
        if (!move_uploaded_file($tmp, $targetPath)) {
            Response::json(['ok' => false, 'message' => 'Unable to store uploaded image'], 500);
        }

        if ($currentFile) {
            $old = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . basename($currentFile);
            if (is_file($old)) {
                @unlink($old);
            }
        }

        return $safeName;
    }
}
