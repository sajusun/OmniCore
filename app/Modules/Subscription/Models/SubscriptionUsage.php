<?php

namespace App\Modules\Subscription\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'feature_code',
        'used',
        'resets_at',
    ];

    protected $casts = [
        'used' => 'integer',
        'resets_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
