<?php

namespace App\Enums;

enum BrandEnum: int
{
    case Nissan = 1;
    case Toyota = 2;
    case Honda = 3;
    case BMW = 4;
    case Mercedes = 5;
    case Audi = 6;
    case Ford = 7;
    case Chevrolet = 8;
    case Hyundai = 9;
    case Kia = 10;

    public function label(): string
    {
        return match ($this) {
            self::Nissan => 'Nissan',
            self::Toyota => 'Toyota',
            self::Honda => 'Honda',
            self::BMW => 'BMW',
            self::Mercedes => 'Mercedes',
            self::Audi => 'Audi',
            self::Ford => 'Ford',
            self::Chevrolet => 'Chevrolet',
            self::Hyundai => 'Hyundai',
            self::Kia => 'Kia',
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