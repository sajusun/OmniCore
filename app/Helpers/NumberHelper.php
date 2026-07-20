<?php

namespace App\Helpers;

class NumberHelper
{
    public static function format($number, int $decimals = 2): string
    {
        return number_format($number, $decimals);
    }

    public static function percentage($value, $total): float
    {
        if ($total == 0) return 0;

        return ($value / $total) * 100;
    }

    public static function currency($amount, string $symbol = '৳'): string
    {
        return $symbol . number_format($amount, 2);
    }

    public static function clamp($value, $min, $max)
    {
        return max($min, min($max, $value));
    }

    public static function isEven(int $number): bool
    {
        return $number % 2 === 0;
    }
}