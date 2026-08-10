<?php

namespace App\Enums;

enum VehicleMotorcycleTypeEnum: int
{
    case HARLEY_DAVIDSON = 1;
    case SPORT_BIKES = 2;
    case CRUISERS = 3;
    case ADVENTURE_TOURING = 4;
    case DIRT_ENDURO = 5;
    case CAFE_RACERS = 6;
    case CHOPPERS_CUSTOMS = 7;
    case SCOOTERS = 8;
    case TRACK_DAYS = 9;
    case CHARITY_RIDES = 10;
    case NIGHT_RIDES = 11;

    public function label(): string
    {
        return match ($this) {
            self::HARLEY_DAVIDSON => 'Harley-Davidson',
            self::SPORT_BIKES => 'Sport Bikes',
            self::CRUISERS => 'Cruisers',
            self::ADVENTURE_TOURING => 'Adventure Touring',
            self::DIRT_ENDURO => 'Dirt & Enduro',
            self::CAFE_RACERS => 'Café Racers',
            self::CHOPPERS_CUSTOMS => 'Choppers & Customs',
            self::SCOOTERS => 'Scooters',
            self::TRACK_DAYS => 'Track Days',
            self::CHARITY_RIDES => 'Charity Rides',
            self::NIGHT_RIDES => 'Night Rides',
        };
    }

    public static function toArray(): array
    {
        return array_map(fn (self $case) => [
            'id' => $case->value,
            'name' => $case->label(),
            'slug' => strtolower(str_replace([' ', '&', '/', '-'], ['_', 'and', '_', '_'], $case->label())),
        ], self::cases());
    }
}