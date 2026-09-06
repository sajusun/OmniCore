<?php

declare(strict_types=1);

namespace App\Modules\Vendor\Enums;

enum PayoutStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed / Transferred',
            self::FAILED => 'Failed / Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'badge-soft-warning',
            self::PROCESSING => 'badge-soft-info',
            self::COMPLETED => 'badge-soft-success',
            self::FAILED => 'badge-soft-danger',
        };
    }
}
