<?php

namespace App\Models;

use App\Modules\Media\Traits\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehiclePart extends Model
{
    use HasMedia;

    protected $table = 'vehicle_parts';

    protected $fillable = [
        'vehicle_id',
        'name',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getPartImageAttribute()
    {
        return $this->firstMedia('part_image');
    }

    public function getPartImageUrlAttribute(): ?string
    {
        return $this->firstMedia('part_image')?->url;
    }
}
