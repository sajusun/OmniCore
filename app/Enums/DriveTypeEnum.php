<?php

namespace App\Enums;

enum DriveTypeEnum: int
{
    case AWD = 1;
    case FWD = 2;
    case RWD = 3;
    case FourWD = 4;

    public function label(): string
    {
        return match ($this) {
            self::AWD => 'AWD',
            self::FWD => 'FWD',
            self::RWD => 'RWD',
            self::FourWD => '4WD',
        };
    }

    public static function options(): array
    {
        return array_map(fn(self $item) => [
            'id' => $item->value,
            'name' => $item->label(),
        ], self::cases());
    }
}