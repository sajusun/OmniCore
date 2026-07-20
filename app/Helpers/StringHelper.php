<?php

namespace App\Helpers;

class StringHelper
{
    public static function slug(string $text): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
    }

    public static function random(int $length = 10): string
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
    }

    public static function mask(string $string, int $visible = 3): string
    {
        $length = strlen($string);

        if ($length <= $visible) return $string;

        return substr($string, 0, $visible) . str_repeat('*', $length - $visible);
    }

    public static function initials(string $name): string
    {
        return collect(explode(' ', $name))
            ->map(fn($word) => strtoupper($word[0]))
            ->implode('');
    }
}