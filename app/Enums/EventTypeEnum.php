<?php

namespace App\Enums;

enum EventTypeEnum: int
{
    case CAR_SHOW = 1;
    case CRUISE_NIGHT = 2;
    case DRIFT_EVENT = 3;
    case MOTORCYCLE_RIDE = 4;
    case MOTOGP_FAN_EVENT = 5;
    case RALLY = 6;

    public function label(): string
    {
        return match($this) {
            self::CAR_SHOW         => 'Car Show',
            self::CRUISE_NIGHT     => 'Cruise Night',
            self::DRIFT_EVENT      => 'Drift Event',
            self::MOTORCYCLE_RIDE  => 'Motorcycle Ride',
            self::MOTOGP_FAN_EVENT => 'MotoGP Fan Event',
            self::RALLY            => 'Rally',
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