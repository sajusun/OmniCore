<?php

declare(strict_types=1);

namespace App\Modules\Order\Enums;

enum PaymentStatus: string
{
    case UNPAID = 'unpaid';
    case PAID = 'paid';
    case REFUNDED = 'refunded';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::UNPAID => 'Unpaid',
            self::PAID => 'Paid',
            self::REFUNDED => 'Refunded',
            self::FAILED => 'Failed',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::UNPAID => 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
            self::PAID => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
            self::REFUNDED => 'bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
            self::FAILED => 'bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400',
        };
    }
}
