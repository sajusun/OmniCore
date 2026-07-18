<?php

namespace App\Enums;

enum TransmissionEnum: int
{
    case Manual = 1;
    case Automatic = 2;

    public function label(): string
    {
        return match ($this) {
            self::Manual => 'Manual',
            self::Automatic => 'Automatic',
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