<?php

namespace App\Modules\AppSupport\Enums;

enum SupportCategory: string
{
    case BUG = 'bug';
    case ACCOUNT = 'account';
    case EVENT = 'event';
    case CLUB = 'club';
    case VEHICLE = 'vehicle';
    case BILLING = 'billing';
    case FEEDBACK = 'feedback';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::BUG => 'Technical Bug / Crash',
            self::ACCOUNT => 'Account & Login',
            self::EVENT => 'Events & RSVP',
            self::CLUB => 'Clubs & Membership',
            self::VEHICLE => 'Vehicles & Garage',
            self::BILLING => 'Billing & Subscription',
            self::FEEDBACK => 'Feedback & Suggestion',
            self::OTHER => 'Other / General Issue',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function forSelect(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], self::cases());
    }
}
