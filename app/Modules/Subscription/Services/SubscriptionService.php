<?php

namespace App\Modules\Subscription\Services;

use App\Models\User;
use App\Modules\Payment\Enums\PaymentMethod;
use App\Modules\Payment\Services\PaymentService;
use App\Modules\Payment\Services\WalletService;
use App\Modules\Subscription\Enums\PlanBillingInterval;
use App\Modules\Subscription\Enums\SubscriptionStatus;
use App\Modules\Subscription\Models\Plan;
use App\Modules\Subscription\Models\PlanFeature;
use App\Modules\Subscription\Models\Subscription;
use App\Modules\Subscription\Models\SubscriptionUsage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class SubscriptionService
{
    public function __construct(
        protected WalletService $walletService,
        protected PaymentService $paymentService
    ) {}

    public function calculatePeriod(Plan $plan, ?Carbon $startDate = null): ?Carbon
    {
        $start = $startDate ? $startDate->copy() : now();
        $count = max(1, $plan->interval_count);

        return match ($plan->billing_interval) {
            PlanBillingInterval::DAY->value, 'day' => $start->addDays($count),
            PlanBillingInterval::WEEK->value, 'week' => $start->addWeeks($count),
            PlanBillingInterval::MONTH->value, 'month' => $start->addMonths($count),
            PlanBillingInterval::YEAR->value, 'year' => $start->addYears($count),
            PlanBillingInterval::LIFETIME->value, 'lifetime' => null,
            default => $start->addMonth(),
        };
    }

    public function subscribe(
        User $user,
        Plan $plan,
        ?string $paymentMethod = 'wallet',
        bool $autoRenew = true
    ): Subscription {
        return DB::transaction(function () use ($user, $plan, $paymentMethod, $autoRenew) {
            // Cancel any active previous subscriptions
            $activeSub = Subscription::where('user_id', $user->id)
                ->whereIn('status', [SubscriptionStatus::ACTIVE->value, SubscriptionStatus::TRIALING->value])
                ->first();

            if ($activeSub && $activeSub->plan_id !== $plan->id) {
                $activeSub->update([
                    'status' => SubscriptionStatus::CANCELED->value,
                    'canceled_at' => now(),
                ]);
            }

            $hasTrial = $plan->hasTrial();
            $trialEndsAt = $hasTrial ? now()->addDays($plan->trial_days) : null;
            $startsAt = now();
            $endsAt = $this->calculatePeriod($plan, $hasTrial ? $trialEndsAt : $startsAt);
            $initialStatus = $hasTrial ? SubscriptionStatus::TRIALING : SubscriptionStatus::ACTIVE;

            $totalCharge = (float) $plan->price + (float) $plan->signup_fee;

            // Handle Payment if not free and not on trial
            if (!$hasTrial && $totalCharge > 0) {
                if ($paymentMethod === 'wallet') {
                    $wallet = $this->walletService->getOrCreateWallet($user);
                    if (!$wallet->hasSufficientBalance($totalCharge)) {
                        throw new RuntimeException('Insufficient wallet balance to subscribe to this plan.');
                    }
                    $this->walletService->pay(
                        user: $user,
                        amount: $totalCharge,
                        description: "Subscription payment for {$plan->name} plan"
                    );
                }
            }

            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => $initialStatus->value,
                'trial_ends_at' => $trialEndsAt,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'auto_renew' => $autoRenew,
                'payment_method' => $paymentMethod,
                'metadata' => [
                    'plan_name' => $plan->name,
                    'price' => (float) $plan->price,
                ],
            ]);

            // Initialize Feature Usages
            foreach ($plan->features as $feature) {
                SubscriptionUsage::create([
                    'subscription_id' => $subscription->id,
                    'feature_code' => $feature->code,
                    'used' => 0,
                    'resets_at' => $endsAt,
                ]);
            }

            // Sync User subscription flag
            $user->update([
                'is_subscribed' => true,
                'subscription_ends_at' => $endsAt,
            ]);

            return $subscription;
        });
    }

    public function cancel(Subscription $subscription, bool $immediately = false): Subscription
    {
        return DB::transaction(function () use ($subscription, $immediately) {
            $subscription->canceled_at = now();
            $subscription->auto_renew = false;

            if ($immediately) {
                $subscription->status = SubscriptionStatus::CANCELED->value;
                $subscription->ends_at = now();
                $subscription->user?->update([
                    'is_subscribed' => false,
                    'subscription_ends_at' => null,
                ]);
            }

            $subscription->save();
            return $subscription;
        });
    }

    public function resume(Subscription $subscription): Subscription
    {
        if (!$subscription->onGracePeriod()) {
            throw new RuntimeException('Only subscriptions in grace period can be resumed.');
        }

        $subscription->update([
            'canceled_at' => null,
            'auto_renew' => true,
            'status' => SubscriptionStatus::ACTIVE->value,
        ]);

        return $subscription;
    }

    public function canAccessFeature(User $user, string $featureCode): bool
    {
        $subscription = Subscription::with('plan.features')
            ->where('user_id', $user->id)
            ->active()
            ->latest('id')
            ->first();

        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        $feature = $subscription->plan->getFeature($featureCode);
        if (!$feature) {
            return false;
        }

        if ($feature->isUnlimited()) {
            return true;
        }

        $usage = SubscriptionUsage::where('subscription_id', $subscription->id)
            ->where('feature_code', $featureCode)
            ->first();

        $used = $usage?->used ?? 0;
        $quota = $feature->getQuota();

        return $used < $quota;
    }

    public function consumeFeature(User $user, string $featureCode, int $quantity = 1): bool
    {
        return DB::transaction(function () use ($user, $featureCode, $quantity) {
            $subscription = Subscription::with('plan.features')
                ->where('user_id', $user->id)
                ->active()
                ->lockForUpdate()
                ->latest('id')
                ->first();

            if (!$subscription || !$subscription->isActive()) {
                return false;
            }

            $feature = $subscription->plan->getFeature($featureCode);
            if (!$feature) {
                return false;
            }

            if ($feature->isUnlimited()) {
                return true;
            }

            $usage = SubscriptionUsage::firstOrCreate(
                ['subscription_id' => $subscription->id, 'feature_code' => $featureCode],
                ['used' => 0, 'resets_at' => $subscription->ends_at]
            );

            $newUsed = $usage->used + $quantity;
            if ($newUsed > $feature->getQuota()) {
                return false;
            }

            $usage->used = $newUsed;
            $usage->save();

            return true;
        });
    }

    public function getRemainingUsage(User $user, string $featureCode): int|string
    {
        $subscription = Subscription::with('plan.features')
            ->where('user_id', $user->id)
            ->active()
            ->latest('id')
            ->first();

        if (!$subscription || !$subscription->isActive()) {
            return 0;
        }

        $feature = $subscription->plan->getFeature($featureCode);
        if (!$feature) {
            return 0;
        }

        if ($feature->isUnlimited()) {
            return 'unlimited';
        }

        $usage = SubscriptionUsage::where('subscription_id', $subscription->id)
            ->where('feature_code', $featureCode)
            ->first();

        $used = $usage?->used ?? 0;
        $quota = $feature->getQuota();

        return max(0, $quota - $used);
    }
}
