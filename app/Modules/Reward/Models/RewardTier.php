<?php

namespace App\Modules\Reward\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class RewardTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'min_points',
        'point_multiplier',
        'discount_percent',
        'perks',
        'icon',
        'color',
        'sort_order',
    ];

    protected $casts = [
        'min_points' => 'integer',
        'point_multiplier' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'perks' => 'array',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($tier) {
            if (empty($tier->slug)) {
                $tier->slug = Str::slug($tier->name);
            }
        });
    }

    public function userRewards(): HasMany
    {
        return $this->hasMany(UserReward::class, 'tier_id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('min_points', 'asc');
    }
}
