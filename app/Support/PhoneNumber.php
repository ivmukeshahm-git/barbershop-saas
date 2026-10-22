<?php

namespace App\Support;

class PhoneNumber
{
    public static function digits(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?? '';
    }

    public static function isValid(string $phone): bool
    {
        return preg_match('/^\+?[0-9().\-\s]+$/', $phone) === 1
            && strlen(self::digits($phone)) >= 7
            && strlen(self::digits($phone)) <= 15;
    }
}
