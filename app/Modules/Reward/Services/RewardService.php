<?php

namespace App\Modules\Reward\Services;

use App\Models\User;
use App\Modules\Payment\Services\WalletService;
use App\Modules\Reward\Enums\PointTransactionType;
use App\Modules\Reward\Models\Badge;
use App\Modules\Reward\Models\PointTransaction;
use App\Modules\Reward\Models\RewardTier;
use App\Modules\Reward\Models\UserReward;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class RewardService
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    public function getOrCreateUserReward(User $user): UserReward
    {
        return UserReward::firstOrCreate(
            ['user_id' => $user->id],
            [
                'points_balance' => 0,
                'lifetime_points' => 0,
                'tier_id' => RewardTier::ordered()->first()?->id,
                'streak_days' => 0,
                'last_checkin_at' => null,
            ]
        );
    }

    public function addPoints(
        User $user,
        int $amount,
        ?Model $reference = null,
        string $description = 'Points earned',
        array $metadata = []
    ): PointTransaction {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Point amount must be greater than zero');
        }

        return DB::transaction(function () use ($user, $amount, $reference, $description, $metadata) {
            $userReward = UserReward::where('user_id', $user->id)->lockForUpdate()->first();
            if (!$userReward) {
                $userReward = $this->getOrCreateUserReward($user);
                $userReward = UserReward::where('user_id', $user->id)->lockForUpdate()->first();
            }

            // Apply tier multiplier if active
            $multiplier = (float) ($userReward->tier?->point_multiplier ?? 1.00);
            $finalPoints = (int) round($amount * $multiplier);

            $beforeBalance = $userReward->points_balance;
            $afterBalance = $beforeBalance + $finalPoints;

            $userReward->points_balance = $afterBalance;
            $userReward->lifetime_points += $finalPoints;

            // Check tier promotion
            $this->evaluateTier($user, $userReward);

            $userReward->save();

            return PointTransaction::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $user->id,
                'amount' => $finalPoints,
                'type' => PointTransactionType::EARNED->value,
                'before_balance' => $beforeBalance,
                'after_balance' => $afterBalance,
                'description' => $description . ($multiplier > 1 ? " ({$multiplier}x Tier Multiplier)" : ''),
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference?->getKey(),
                'metadata' => array_merge($metadata, ['raw_amount' => $amount, 'multiplier' => $multiplier]),
            ]);
        });
    }

    public function spendPoints(
        User $user,
        int $amount,
        ?Model $reference = null,
        string $description = 'Points spent'
    ): PointTransaction {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Point amount must be greater than zero');
        }

        return DB::transaction(function () use ($user, $amount, $reference, $description) {
            $userReward = UserReward::where('user_id', $user->id)->lockForUpdate()->first();
            if (!$userReward || $userReward->points_balance < $amount) {
                throw new RuntimeException('Insufficient points balance.');
            }

            $beforeBalance = $userReward->points_balance;
            $afterBalance = $beforeBalance - $amount;

            $userReward->points_balance = $afterBalance;
            $userReward->save();

            return PointTransaction::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $user->id,
                'amount' => -$amount,
                'type' => PointTransactionType::SPENT->value,
                'before_balance' => $beforeBalance,
                'after_balance' => $afterBalance,
                'description' => $description,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference?->getKey(),
            ]);
        });
    }

    public function redeemPointsToWallet(User $user, int $points, float $rate = 0.01): array
    {
        if ($points <= 0) {
            throw new InvalidArgumentException('Points to redeem must be greater than zero');
        }

        return DB::transaction(function () use ($user, $points, $rate) {
            $userReward = UserReward::where('user_id', $user->id)->lockForUpdate()->first();
            if (!$userReward || $userReward->points_balance < $points) {
                throw new RuntimeException('Insufficient points balance to redeem.');
            }

            $cashValue = round($points * $rate, 2);
            if ($cashValue <= 0) {
                throw new RuntimeException('Points must amount to at least $0.01');
            }

            $beforeBalance = $userReward->points_balance;
            $afterBalance = $beforeBalance - $points;
            $userReward->points_balance = $afterBalance;
            $userReward->save();

            $pointTrx = PointTransaction::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $user->id,
                'amount' => -$points,
                'type' => PointTransactionType::REDEEMED->value,
                'before_balance' => $beforeBalance,
                'after_balance' => $afterBalance,
                'description' => "Redeemed {$points} points for \${$cashValue} Wallet Credit",
                'metadata' => ['cash_value' => $cashValue, 'rate' => $rate],
            ]);

            // Credit user's wallet
            $walletTrx = $this->walletService->deposit(
                user: $user,
                amount: $cashValue,
                description: "Points redemption ({$points} points)"
            );

            return [
                'points_deducted' => $points,
                'cash_credited' => $cashValue,
                'new_points_balance' => $afterBalance,
                'new_wallet_balance' => (float) $walletTrx->balance_after,
            ];
        });
    }

    public function claimDailyCheckin(User $user): array
    {
        return DB::transaction(function () use ($user) {
            $userReward = UserReward::where('user_id', $user->id)->lockForUpdate()->first();
            if (!$userReward) {
                $userReward = $this->getOrCreateUserReward($user);
                $userReward = UserReward::where('user_id', $user->id)->lockForUpdate()->first();
            }

            if (!$userReward->canCheckInToday()) {
                throw new RuntimeException('You have already claimed your daily check-in bonus today!');
            }

            // Streak calculation
            $lastCheckin = $userReward->last_checkin_at;
            if ($lastCheckin && $lastCheckin->isYesterday()) {
                $newStreak = $userReward->streak_days + 1;
            } else {
                $newStreak = 1;
            }

            // Base daily checkin reward = 10 pts, +5 bonus per streak day up to 50 pts
            $basePoints = 10;
            $streakBonus = min(40, ($newStreak - 1) * 5);
            $totalPointsEarned = $basePoints + $streakBonus;

            $beforeBalance = $userReward->points_balance;
            $afterBalance = $beforeBalance + $totalPointsEarned;

            $userReward->points_balance = $afterBalance;
            $userReward->lifetime_points += $totalPointsEarned;
            $userReward->streak_days = $newStreak;
            $userReward->last_checkin_at = now();

            $this->evaluateTier($user, $userReward);
            $userReward->save();

            PointTransaction::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $user->id,
                'amount' => $totalPointsEarned,
                'type' => PointTransactionType::EARNED->value,
                'before_balance' => $beforeBalance,
                'after_balance' => $afterBalance,
                'description' => "Day {$newStreak} Check-in Bonus (+{$totalPointsEarned} pts)",
                'metadata' => ['streak_days' => $newStreak, 'streak_bonus' => $streakBonus],
            ]);

            // Check streak badges
            $this->checkAndAwardBadge($user, 'checkin_streak', $newStreak);

            return [
                'points_earned' => $totalPointsEarned,
                'streak_days' => $newStreak,
                'new_points_balance' => $afterBalance,
            ];
        });
    }

    public function evaluateTier(User $user, UserReward $userReward): ?RewardTier
    {
        $eligibleTier = RewardTier::where('min_points', '<=', $userReward->lifetime_points)
            ->orderBy('min_points', 'desc')
            ->first();

        if ($eligibleTier && $eligibleTier->id !== $userReward->tier_id) {
            $userReward->tier_id = $eligibleTier->id;
        }

        return $eligibleTier;
    }

    public function checkAndAwardBadge(User $user, string $criteriaType, int $currentValue): ?Badge
    {
        $eligibleBadge = Badge::active()
            ->where('criteria_type', $criteriaType)
            ->where('criteria_threshold', '<=', $currentValue)
            ->whereDoesntHave('users', fn($q) => $q->where('user_id', $user->id))
            ->orderBy('criteria_threshold', 'desc')
            ->first();

        if ($eligibleBadge) {
            $user->badges()->attach($eligibleBadge->id, ['awarded_at' => now()]);

            if ($eligibleBadge->points_reward > 0) {
                $this->addPoints(
                    user: $user,
                    amount: $eligibleBadge->points_reward,
                    reference: $eligibleBadge,
                    description: "Badge unlocked: {$eligibleBadge->name}"
                );
            }
        }

        return $eligibleBadge;
    }
}
