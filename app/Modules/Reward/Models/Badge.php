<?php

namespace App\Modules\Reward\Models;

use App\Models\User;
use App\Modules\Reward\Enums\BadgeType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'badge_type',
        'points_reward',
        'criteria_type',
        'criteria_threshold',
        'is_active',
    ];

    protected $casts = [
        'points_reward' => 'integer',
        'criteria_threshold' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($badge) {
            if (empty($badge->slug)) {
                $badge->slug = Str::slug($badge->name);
            }
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->withPivot('awarded_at')
            ->withTimestamps();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
