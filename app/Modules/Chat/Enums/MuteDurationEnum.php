<?php

namespace App\Modules\Chat\Enums;

use Carbon\Carbon;

enum MuteDurationEnum: string
{
    case ONE_HOUR    = '1_hour';
    case EIGHT_HOURS = '8_hours';
    case ONE_DAY     = '1_day';
    case SEVEN_DAYS  = '7_days';
    case FOREVER     = 'forever';
    case CUSTOM      = 'custom';

    public function toCarbon(?string $customTime = null): ?Carbon
    {
        return match ($this) {
            self::ONE_HOUR    => now()->addHour(),
            self::EIGHT_HOURS => now()->addHours(8),
            self::ONE_DAY     => now()->addDay(),
            self::SEVEN_DAYS  => now()->addDays(7),
            self::FOREVER     => now()->addYears(10),
            self::CUSTOM      => $customTime ? Carbon::parse($customTime) : null,
        };
    }
}
