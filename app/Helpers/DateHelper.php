<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    public static function now(): Carbon
    {
        return Carbon::now();
    }

    public static function format($date, string $format = 'Y-m-d H:i:s'): string
    {
        return Carbon::parse($date)->format($format);
    }

    public static function human($date): string
    {
        return Carbon::parse($date)->diffForHumans();
    }

    public static function age($date): int
    {
        return Carbon::parse($date)->age;
    }

    public static function addDays($days): Carbon
    {
        return Carbon::now()->addDays($days);
    }

    public static function isPast($date): bool
    {
        return Carbon::parse($date)->isPast();
    }
}