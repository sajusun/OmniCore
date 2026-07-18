<?php

namespace App\Enums;

enum VehicleTypeEnum: int
{
    case Car = 1;
    case Motorcycle = 2;
    case Truck = 3;
    case OffRoad = 4;

    public function label(): string
    {
        return match ($this) {
            self::Car => 'Car',
            self::Motorcycle => 'Motorcycle',
            self::Truck => 'Truck',
            self::OffRoad => 'Off-Road',
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