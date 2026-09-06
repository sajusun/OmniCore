<?php

declare(strict_types=1);

namespace App\Modules\Affiliate\Enums;

enum ReferralStatus: string
{
    case PENDING = 'pending';
    case CONVERTED = 'converted';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::CONVERTED => 'Converted',
            self::PAID => 'Commission Paid',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'badge-soft-info',
            self::CONVERTED => 'badge-soft-success',
            self::PAID => 'badge-soft-primary',
            self::CANCELLED => 'badge-soft-danger',
        };
    }
}
