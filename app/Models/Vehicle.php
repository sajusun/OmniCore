<?php

namespace App\Models;

use App\Enums\BrandEnum;
use App\Enums\DriveTypeEnum;
use App\Enums\VehicleTypeEnum;
use App\Enums\TransmissionEnum;
use App\Modules\Media\Traits\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    use HasMedia;
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
        
        'performance_mods',
        'exterior_mods',
        'suspension',

        'build_story',
    ];

    protected $casts = [
        'brand'             => BrandEnum::class,
        'vehicle_type'      => VehicleTypeEnum::class,
        'transmission'      => TransmissionEnum::class,
        'drive_type'        => DriveTypeEnum::class,

        'year'              => 'integer',
        'horsepower'        => 'integer',
        'mileage'           => 'integer',

        'performance_mods'   => 'array',
        'exterior_mods'     => 'array',
        'suspension'        => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function garage()
    {
        return $this->belongsTo(Garage::class);
    }

    public function parts()
    {
        return $this->hasMany(VehiclePart::class);
    }
}
