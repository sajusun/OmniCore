<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FirebaseToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'device_id',
        'device_name',
        'platform',
        'jwt_hash',
        'ip_address',
        'user_agent',
        'last_activity_at',
        'status',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
    ];

    /**
     * User Relation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}