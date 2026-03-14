<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Response;
use App\Core\Validator;
use App\Models\Experience;
use App\Models\Qualification;
use App\Models\User;

class ProfileController extends Controller
{
    public function page(): void
    {
        if (!Auth::check()) {
            Response::redirect((string) ($this->config['base_path'] ?? '/Ems/public') . '/login');
        }

        $userId = Auth::userId();
        $user = (new User($this->config))->findById((int) $userId);
        if (!$user) {
            Auth::logout();
            Response::redirect((string) ($this->config['base_path'] ?? '/Ems/public') . '/login');
        }

        $qualifications = (new Qualification($this->config))->forUser((int) $userId);
        $experiences = (new Experience($this->config))->forUser((int) $userId);

        $this->render('profile/show', [
            'title' => 'My Profile',
            'user' => $user,
            'qualifications' => $qualifications,
            'experiences' => $experiences,
        ]);
    }

    public function update(): void
    {
        if (!Auth::check()) {
            Response::json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            Response::json(['ok' => false, 'message' => 'Invalid CSRF token'], 419);
        }

        $userId = (int) Auth::userId();
        $userModel = new User($this->config);
        $existing = $userModel->findById($userId);
        if (!$existing) {
            Auth::logout();
            Response::json(['ok' => false, 'message' => 'User not found'], 404);
        }

        $fullName = trim($_POST['full_name'] ?? '');
        $age = $_POST['age'] ?? null;

        $payload = [
            'full_name' => $fullName,
            'age' => (int) $age,
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

        if (!Validator::required($fullName) || !Validator::safeText($fullName, 120)) {
            Response::json(['ok' => false, 'message' => 'Valid full name is required'], 422);
        }
        if (!Validator::integerBetween($age, 18, 80)) {
            Response::json(['ok' => false, 'message' => 'Age must be between 18 and 80'], 422);
        }

        foreach ($payload as $key => $value) {
            if (in_array($key, ['full_name', 'age', 'profile_picture'], true)) {
                continue;
            }
            if (!str_ends_with($key, 'line2') && !Validator::required($value)) {
                Response::json(['ok' => false, 'message' => 'Please fill all required address fields'], 422);
            }
            if (!Validator::safeText((string) $value, 150)) {
                Response::json(['ok' => false, 'message' => 'Address values are too long'], 422);
            }
        }

        $quals = array_values(array_filter($_POST['qualifications'] ?? [], static fn ($v) => trim((string) $v) !== ''));
        $exps = array_values(array_filter($_POST['experiences'] ?? [], static fn ($v) => trim((string) $v) !== ''));

        if (count($quals) === 0 || count($exps) === 0) {
            Response::json(['ok' => false, 'message' => 'At least one qualification and one experience are required'], 422);
        }

        $payload['profile_picture'] = $this->handleProfileUpload($existing['profile_picture']);
        $userModel->updateProfile($userId, $payload);
        (new Qualification($this->config))->saveMany($userId, $quals);
        (new Experience($this->config))->saveMany($userId, $exps);

        Response::json(['ok' => true, 'message' => 'Profile updated successfully']);
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
