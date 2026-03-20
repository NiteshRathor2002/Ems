<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Mailer;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\PasswordResetOtp;
use App\Models\User;

class ForgotpasswordController extends Controller
{
    public function otp(): void
    {
        $base = (string) ($this->config['base_path'] ?? '/Ems/public');

        if (Auth::check()) {
            Response::redirect($base . '/dashboard');
        }

        $this->render('auth/forgot_password', [
            'title' => 'Forgot Password',
        ]);
    }

    public function sendOtp(): void
    {
        $base = (string) ($this->config['base_path'] ?? '/Ems/public');

        if (Auth::check()) {
            Response::redirect($base . '/dashboard');
        }
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            Session::flash('error', 'Invalid CSRF token.');
            Response::redirect($base . '/employee/forgot');
        }

        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        if (!Validator::email($email)) {
            Session::flash('error', 'Please enter a valid email.');
            Response::redirect($base . '/employee/forgot');
        }

        $userModel = new User($this->config);
        $user = $userModel->findByEmail($email);
        if (!$user) {
            Session::flash('error', 'Email address not found.');
            Response::redirect($base . '/employee/forgot');
        }

        $otpModel = new PasswordResetOtp($this->config);
        if (!$otpModel->featureReady()) {
            Session::flash('error', "Database update required: import 'password_reset_otps' table from database/schema.sql.");
            Response::redirect($base . '/employee/forgot');
        }

        $otp = (string) random_int(1000, 9999);
        $saved = $otpModel->createForEmail($email, $otp, 10);
        if (!$saved) {
            Session::flash('error', 'Unable to generate OTP. Please try again.');
            Response::redirect($base . '/employee/forgot');
        }

        $subject = 'Password Reset OTP';
        $message = "Your OTP is: {$otp}\nThis code expires in 10 minutes.";
        $mailer = new Mailer($this->config);
        $sent = $mailer->send($email, $subject, $message);
        if (!$sent) {
            $detail = $mailer->lastError();
            $msg = $detail ? 'Unable to send OTP email: ' . $detail : 'Unable to send OTP email. Please try again.';
            Session::flash('error', $msg);
            Response::redirect($base . '/employee/forgot');
        }

        Session::set('password_reset_email', $email);
        Session::flash('success', 'OTP sent to your email.');
        Response::redirect($base . '/forgot-password/otp');
    }

    public function otpPage(): void
    {
        $base = (string) ($this->config['base_path'] ?? '/Ems/public');

        if (Auth::check()) {
            Response::redirect($base . '/dashboard');
        }

        $email = (string) Session::get('password_reset_email', '');
        if ($email === '') {
            Session::flash('error', 'Please enter your email to receive OTP.');
            Response::redirect($base . '/employee/forgot');
        }

        $this->render('auth/otp', [
            'title' => 'Enter OTP',
            'email' => $email,
        ]);
    }

    public function verifyOtp(): void
    {
        $base = (string) ($this->config['base_path'] ?? '/Ems/public');

        if (Auth::check()) {
            Response::redirect($base . '/dashboard');
        }
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            Session::flash('error', 'Invalid CSRF token.');
            Response::redirect($base . '/forgot-password/otp');
        }

        $email = (string) Session::get('password_reset_email', '');
        if ($email === '') {
            Session::flash('error', 'Please enter your email to receive OTP.');
            Response::redirect($base . '/employee/forgot');
        }

        $otp = trim((string) ($_POST['otp'] ?? ''));
        if ($otp === '') {
            $otp = (string) ($_POST['otp1'] ?? '') . (string) ($_POST['otp2'] ?? '') . (string) ($_POST['otp3'] ?? '') . (string) ($_POST['otp4'] ?? '');
        }
        if (!preg_match('/^\d{4}$/', $otp)) {
            Session::flash('error', 'Please enter a valid 4-digit OTP.');
            Response::redirect($base . '/forgot-password/otp');
        }

        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['password_confirm'] ?? '');
        if (!Validator::minLength($password, 8)) {
            Session::flash('error', 'Password must be at least 8 characters.');
            Response::redirect($base . '/forgot-password/otp');
        }
        if ($password !== $confirm) {
            Session::flash('error', 'Passwords do not match.');
            Response::redirect($base . '/forgot-password/otp');
        }

        $userModel = new User($this->config);
        $user = $userModel->findByEmail($email);
        if (!$user) {
            Session::flash('error', 'Email address not found.');
            Response::redirect($base . '/employee/forgot');
        }

        $otpModel = new PasswordResetOtp($this->config);
        if (!$otpModel->featureReady()) {
            Session::flash('error', "Database update required: import 'password_reset_otps' table from database/schema.sql.");
            Response::redirect($base . '/employee/forgot');
        }

        if (!$otpModel->verify($email, $otp)) {
            Session::flash('error', 'Invalid or expired OTP.');
            Response::redirect($base . '/forgot-password/otp');
        }

        $userModel->updatePassword((int) $user['id'], password_hash($password, PASSWORD_DEFAULT));
        Session::remove('password_reset_email');
        Session::flash('success', 'Password updated successfully. Please login.');
        Response::redirect($base . '/login');
    }
}
