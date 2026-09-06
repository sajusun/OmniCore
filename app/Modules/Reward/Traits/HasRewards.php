<?php

namespace App\Modules\Reward\Traits;

use App\Modules\Reward\Models\Badge;
use App\Modules\Reward\Models\PointTransaction;
use App\Modules\Reward\Models\RewardTier;
use App\Modules\Reward\Models\UserReward;
use App\Modules\Reward\Services\RewardService;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasRewards
{
    public function userReward(): HasOne
    {
        return $this->hasOne(UserReward::class);
    }

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class)->latest('id');
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot('awarded_at')
            ->withTimestamps();
    }

    public function getRewardProfileAttribute(): UserReward
    {
        return app(RewardService::class)->getOrCreateUserReward($this);
    }

    public function getPointsBalanceAttribute(): int
    {
        return $this->rewardProfile->points_balance;
    }

    public function getTierAttribute(): ?RewardTier
    {
        return $this->rewardProfile->tier;
    }

    public function addRewardPoints(int $amount, string $description = 'Points earned'): PointTransaction
    {
        return app(RewardService::class)->addPoints($this, $amount, null, $description);
    }

    public function redeemPoints(int $points): array
    {
        return app(RewardService::class)->redeemPointsToWallet($this, $points);
    }

    public function claimDailyCheckin(): array
    {
        return app(RewardService::class)->claimDailyCheckin($this);
    }
}
