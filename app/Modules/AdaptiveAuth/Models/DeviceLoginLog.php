<?php

declare(strict_types=1);

namespace App\Modules\AdaptiveAuth\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DeviceLoginLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'device_login_logs';

    protected $fillable = [
        'authenticatable_type',
        'authenticatable_id',
        'device_id',
        'ip_address',
        'location',
        'device_name',
        'user_agent',
        'status',
        'failure_reason',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(UserDevice::class, 'device_id');
    }
}
