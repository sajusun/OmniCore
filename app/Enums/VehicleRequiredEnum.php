<?php

namespace App\Enums;

enum VehicleRequiredEnum: int
{
    case ALL_VEHICLES = 1;
    case JAPANESE = 2;
    case EUROPEAN = 3;
    case AMERICAN = 4;
    case AUSTRALIAN = 5;
    case MOTORCYCLES = 6;
    case ELECTRIC = 7;

    public function label(): string
    {
        return match($this) {
            self::ALL_VEHICLES => 'All Vehicles',
            self::JAPANESE     => 'Japanese',
            self::EUROPEAN     => 'European',
            self::AMERICAN     => 'American',
            self::AUSTRALIAN   => 'Australian',
            self::MOTORCYCLES  => 'Motorcycles',
            self::ELECTRIC    => 'Electric',
        };
    }

    public static function toArray(): array
    {
        return array_map(fn($case) => [
            'id'    => $case->value,
            'name'  => $case->label(),
            'slug'  => strtolower(str_replace(' ', '_', $case->label())),
        ], self::cases());
    }
}
