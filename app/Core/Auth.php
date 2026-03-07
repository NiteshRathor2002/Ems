<?php

declare(strict_types=1);

namespace App\Core;

class Auth
{
    public static function login(int $userId, bool $isAdmin = false): void
    {
        Session::set('user_id', $userId);
        Session::set('is_admin', $isAdmin);
    }

    public static function logout(): void
    {
        Session::destroy();
    }

    public static function userId(): ?int
    {
        $id = Session::get('user_id');
        return is_int($id) ? $id : (is_numeric($id) ? (int) $id : null);
    }

    public static function check(): bool
    {
        return self::userId() !== null;
    }

    public static function isAdmin(): bool
    {
        return (bool) Session::get('is_admin', false);
    }
}
