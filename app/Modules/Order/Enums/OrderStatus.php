<?php

declare(strict_types=1);

namespace App\Modules\Order\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case OUT_FOR_DELIVERY = 'out_for_delivery';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::CONFIRMED => 'Confirmed',
            self::PROCESSING => 'Processing',
            self::SHIPPED => 'Shipped',
            self::OUT_FOR_DELIVERY => 'Out for Delivery',
            self::DELIVERED => 'Delivered',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
        };
    }

    /**
     * Get badge color class for UI
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
            self::CONFIRMED, self::PROCESSING => 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
            self::SHIPPED, self::OUT_FOR_DELIVERY => 'bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
            self::DELIVERED => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
            self::CANCELLED => 'bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400',
            self::REFUNDED => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
        };
    }

    /**
     * Validate state machine transition logic
     */
    public function canTransitionTo(self $target): bool
    {
        if ($this === $target) {
            return true;
        }

        return match ($this) {
            self::PENDING => in_array($target, [self::CONFIRMED, self::CANCELLED]),
            self::CONFIRMED => in_array($target, [self::PROCESSING, self::CANCELLED]),
            self::PROCESSING => in_array($target, [self::SHIPPED, self::CANCELLED]),
            self::SHIPPED => in_array($target, [self::OUT_FOR_DELIVERY, self::DELIVERED, self::REFUNDED]),
            self::OUT_FOR_DELIVERY => in_array($target, [self::DELIVERED, self::REFUNDED]),
            self::DELIVERED => $target === self::REFUNDED,
            self::CANCELLED, self::REFUNDED => false, // Terminal states
        };
    }
}
