<?php

declare(strict_types=1);

namespace App\Modules\Affiliate\Enums;

enum CommissionType: string
{
    case PERCENTAGE = 'percentage';
    case FIXED = 'fixed';

    public function label(): string
    {
        return match ($this) {
            self::PERCENTAGE => 'Percentage (%)',
            self::FIXED => 'Fixed Amount ($)',
        };
    }
}
