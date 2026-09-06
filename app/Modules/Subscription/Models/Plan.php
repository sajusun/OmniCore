<?php

namespace App\Modules\Subscription\Models;

use App\Modules\Subscription\Enums\PlanBillingInterval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'price',
        'signup_fee',
        'currency',
        'billing_interval',
        'interval_count',
        'trial_days',
        'is_popular',
        'is_active',
        'sort_order',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'signup_fee' => 'decimal:2',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'interval_count' => 'integer',
        'trial_days' => 'integer',
        'sort_order' => 'integer',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($plan) {
            if (empty($plan->uuid)) {
                $plan->uuid = (string) Str::uuid();
            }
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });
    }

    public function features(): HasMany
    {
        return $this->hasMany(PlanFeature::class)->orderBy('sort_order', 'asc');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order', 'asc');
    }

    public function isFree(): bool
    {
        return (float) $this->price <= 0.00;
    }

    public function hasTrial(): bool
    {
        return $this->trial_days > 0;
    }

    public function getFeature(string $code): ?PlanFeature
    {
        return $this->features->firstWhere('code', $code);
    }
}
