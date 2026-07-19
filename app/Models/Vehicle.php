<?php

namespace App\Models;

use App\Enums\BrandEnum;
use App\Enums\DriveTypeEnum;
use App\Enums\VehicleTypeEnum;
use App\Enums\TransmissionEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    protected $fillable = [
        'garage_id',
        'user_id',
        'name',
        'brand',
        'model',
        'year',
        'vehicle_type',
        'transmission',
        'drive_type',
        'horsepower',
        'mileage',
        'engine',
        'color',
        'vin',
        'description',
    ];

    protected $casts = [
        'brand'         => BrandEnum::class,
        'vehicle_type'  => VehicleTypeEnum::class,
        'transmission'  => TransmissionEnum::class,
        'drive_type'    => DriveTypeEnum::class,
        'year'          => 'integer',
        'horsepower'    => 'integer',
        'mileage'       => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function garage()
    {
        return $this->belongsTo(Garage::class);
    }
}
