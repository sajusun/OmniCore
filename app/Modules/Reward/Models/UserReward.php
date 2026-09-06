<?php

namespace App\Modules\Reward\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points_balance',
        'lifetime_points',
        'tier_id',
        'streak_days',
        'last_checkin_at',
    ];

    protected $casts = [
        'points_balance' => 'integer',
        'lifetime_points' => 'integer',
        'streak_days' => 'integer',
        'last_checkin_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tier(): BelongsTo
    {
        return $this->belongsTo(RewardTier::class, 'tier_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class, 'user_id', 'user_id')->latest('id');
    }

    public function canCheckInToday(): bool
    {
        if (is_null($this->last_checkin_at)) {
            return true;
        }

        return !$this->last_checkin_at->isToday();
    }
}
