<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'billing_cycle',
        'title',
        'logo',
        'bg',
        'features',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'float',
    ];

    public function items()
    {
        return $this->hasMany(SubscriptionPlanItem::class)->where('status', true)->orderBy('order');
    }
}
