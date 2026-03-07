<?php

declare(strict_types=1);

namespace App\Core;

class Csrf
{
    public static function token(): string
    {
        if (!Session::has('_csrf')) {
            Session::set('_csrf', bin2hex(random_bytes(32)));
        }
        return (string) Session::get('_csrf');
    }

    public static function verify(?string $token): bool
    {
        $sessionToken = (string) Session::get('_csrf', '');
        return $sessionToken !== '' && $token !== null && hash_equals($sessionToken, $token);
    }
}
