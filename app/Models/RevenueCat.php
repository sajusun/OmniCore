<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevenueCat extends Model
{
    protected $table = 'revenue_cat_subscriptions';

    protected $fillable = [
        'user_id',
        'revenuecat_id',
        'product_id',
        'package',
        'is_active',
        'started_at',
        'expires_at',
        'meta',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }
}
