<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlanItem extends Model
{
    protected $fillable = [
        'subscription_plan_id',
        'key',
        'title',
        'value',
        'order',
        'status',
    ];

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}
