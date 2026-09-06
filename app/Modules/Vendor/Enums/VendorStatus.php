<?php

declare(strict_types=1);

namespace App\Modules\Vendor\Enums;

enum VendorStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending Verification',
            self::ACTIVE => 'Active & Verified',
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
