<?php

namespace App\Modules\AppSupport\Enums;

enum SupportStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case REPLIED = 'replied';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::IN_PROGRESS => 'In Progress',
            self::REPLIED => 'Replied',
            self::RESOLVED => 'Resolved',
            self::CLOSED => 'Closed',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-warning text-dark',
            self::IN_PROGRESS => 'bg-info text-white',
            self::REPLIED => 'bg-primary text-white',
            self::RESOLVED => 'bg-success text-white',
            self::CLOSED => 'bg-secondary text-white',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
