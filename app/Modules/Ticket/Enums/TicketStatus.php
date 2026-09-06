<?php

declare(strict_types=1);

namespace App\Modules\Ticket\Enums;

enum TicketStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case WAITING_ON_USER = 'waiting_on_user';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Open',
            self::IN_PROGRESS => 'In Progress',
            self::WAITING_ON_USER => 'Waiting on User',
            self::RESOLVED => 'Resolved',
            self::CLOSED => 'Closed',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::OPEN => 'badge-soft-primary',
            self::IN_PROGRESS => 'badge-soft-warning',
            self::WAITING_ON_USER => 'badge-soft-info',
            self::RESOLVED => 'badge-soft-success',
            self::CLOSED => 'badge-soft-secondary',
        };
    }
}
