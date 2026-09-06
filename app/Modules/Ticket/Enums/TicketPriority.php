<?php

declare(strict_types=1);

namespace App\Modules\Ticket\Enums;

enum TicketPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
            self::URGENT => 'Urgent',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::LOW => 'badge-soft-info',
            self::MEDIUM => 'badge-soft-primary',
            self::HIGH => 'badge-soft-warning',
            self::URGENT => 'badge-soft-danger',
        };
    }
}
