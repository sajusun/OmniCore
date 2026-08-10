<?php

namespace App\Enums;

enum VehicleCarTypeEnum: int
{
    case JDM = 1;
    case EURO = 2;
    case AMERICAN = 3;
    case AUSTRALIAN = 4;
    case MUSCLE_CARS = 5;
    case HOT_RODS_CUSTOMS = 6;
    case TRUCKS_UTES = 7;
    case SUPERCARS_HYPERCARS = 8;
    case EVS = 9;
    case PERFORMANCE_TUNERS = 10;
    case DRIFT = 11;
    case DRAG_RACING = 12;
    case CIRCUIT_TRACK_DAYS = 13;
    case FOUR_X_FOUR_OFF_ROAD = 14;
    case CARS_COFFEE = 15;
    case NIGHT_MEETS = 16;
    case MIXED_CAR_MEETS = 17;
    case CHARITY_CRUISES = 18;
    case CLASSICS_VINTAGE = 19;

    public function label(): string
    {
        return match ($this) {
            self::JDM => 'JDM',
            self::EURO => 'Euro',
            self::AMERICAN => 'American',
            self::AUSTRALIAN => 'Australian',
            self::MUSCLE_CARS => 'Muscle Cars',
            self::HOT_RODS_CUSTOMS => 'Hot Rods & Customs',
            self::TRUCKS_UTES => 'Trucks & Utes',
            self::SUPERCARS_HYPERCARS => 'Supercars & Hypercars',
            self::EVS => 'EVs',
            self::PERFORMANCE_TUNERS => 'Performance & Tuners',
            self::DRIFT => 'Drift',
            self::DRAG_RACING => 'Drag Racing',
            self::CIRCUIT_TRACK_DAYS => 'Circuit & Track Days',
            self::FOUR_X_FOUR_OFF_ROAD => '4x4 & Off-Road',
            self::CARS_COFFEE => 'Cars & Coffee',
            self::NIGHT_MEETS => 'Night Meets',
            self::MIXED_CAR_MEETS => 'Mixed Car Meets',
            self::CHARITY_CRUISES => 'Charity Cruises',
            self::CLASSICS_VINTAGE => 'Classics & Vintage',
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