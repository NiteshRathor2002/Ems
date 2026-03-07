<?php

declare(strict_types=1);

namespace App\Core;

class Validator
{
    public static function required(string $value): bool
    {
        return trim($value) !== '';
    }

    public static function email(string $value): bool
    {
        return (bool) filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public static function minLength(string $value, int $length): bool
    {
        return mb_strlen(trim($value)) >= $length;
    }

    public static function integerBetween(mixed $value, int $min, int $max): bool
    {
        if (!is_numeric($value)) {
            return false;
        }
        $n = (int) $value;
        return $n >= $min && $n <= $max;
    }

    public static function safeText(string $value, int $max = 255): bool
    {
        return mb_strlen(trim($value)) <= $max;
    }
}
