<?php

declare(strict_types=1);

namespace App\Modules\Affiliate\Enums;

enum AffiliateStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending Review',
            self::ACTIVE => 'Active',
            self::SUSPENDED => 'Suspended',
            self::REJECTED => 'Rejected',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'badge-soft-warning',
            self::ACTIVE => 'badge-soft-success',
            self::SUSPENDED => 'badge-soft-danger',
            self::REJECTED => 'badge-soft-secondary',
        };
    }
}
