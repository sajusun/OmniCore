<?php

namespace App\Modules\Subscription\Traits;

use App\Modules\Subscription\Models\Subscription;
use App\Modules\Subscription\Services\SubscriptionService;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasSubscriptions
{
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class)->latest('id');
    }

    public function currentSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->active()
            ->with(['plan.features', 'usages'])
            ->first();
    }

    public function subscribed(?string $planSlug = null): bool
    {
        $current = $this->currentSubscription();
        if (!$current || !$current->isActive()) {
            return false;
        }

        if ($planSlug) {
            return $current->plan?->slug === $planSlug;
        }

        return true;
    }

    public function onTrial(): bool
    {
        return (bool) $this->currentSubscription()?->onTrial();
    }

    public function canAccessFeature(string $featureCode): bool
    {
        return app(SubscriptionService::class)->canAccessFeature($this, $featureCode);
    }

    public function consumeFeature(string $featureCode, int $quantity = 1): bool
    {
        return app(SubscriptionService::class)->consumeFeature($this, $featureCode, $quantity);
    }

    public function getRemainingFeatureUsage(string $featureCode): int|string
    {
        return app(SubscriptionService::class)->getRemainingUsage($this, $featureCode);
    }
}
