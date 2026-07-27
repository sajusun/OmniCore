<?php

namespace App\Services;

use App\Models\User;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;

class LocationService
{

    public function format(float $distance): string
    {
        if ($distance < 1) {
            return round($distance * 1000) . ' m';
        }

        return round($distance, 2) . ' km';
    }

    
    /**
     * Calculate distance between two coordinates.
     *
     * @param  string  $unit  km|mile
     */
    public function getDistance(float $fromLat, float $fromLng, float $toLat, float $toLng, string $unit = 'km'): float
    {
        $earthRadius = $unit === 'mile' ? 3958.8 : 6371;

        $latFrom = deg2rad($fromLat);
        $lngFrom = deg2rad($fromLng);

        $latTo = deg2rad($toLat);
        $lngTo = deg2rad($toLng);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)));

        return round($earthRadius * $angle, 2);
    }

    public function nearbyUsers(float $latitude, float $longitude, float $radius = 20): Builder
    {

        return User::query()->select('*')->selectRaw(
            '(6371 *acos(cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
            [$latitude, $longitude, $latitude]
        )->having('distance', '<=', $radius)->orderBy('distance');
    }

    public function nearbyEvents(float $latitude, float $longitude, float $radius = 20): Builder
    {
        return Event::query()->select('*')->selectRaw(
            '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) *
         cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))) AS distance',
            [$latitude, $longitude, $latitude]
        )->having('distance', '<=', $radius)->orderBy('distance');
    }
}
