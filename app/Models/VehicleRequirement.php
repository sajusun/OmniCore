<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleRequirement extends Model
{
    protected $table = 'vehicle_requirements';

    protected $fillable = [
        'id',
        'name',
        'slug',
    ];
}
