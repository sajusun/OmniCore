<?php

declare(strict_types=1);

namespace App\Modules\Review\Enums;

enum ReviewStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case FLAGGED = 'flagged';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending Moderation',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::FLAGGED => 'Flagged / Reported',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'badge-soft-warning',
            self::APPROVED => 'badge-soft-success',
            self::REJECTED => 'badge-soft-danger',
            self::FLAGGED => 'badge-soft-dark',
        };
    }
}
