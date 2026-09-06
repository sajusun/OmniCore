<?php

namespace App\Modules\Subscription\Models;

use App\Models\User;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Traits\Payable;
use App\Modules\Subscription\Enums\SubscriptionStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Subscription extends Model
{
    use HasFactory, Payable;

    protected $fillable = [
        'uuid',
        'user_id',
        'plan_id',
        'status',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'canceled_at',
        'auto_renew',
        'payment_method',
        'metadata',
    ];

    protected $casts = [
        'status' => SubscriptionStatus::class,
        'trial_ends_at' => 'datetime',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_at' => 'datetime',
        'auto_renew' => 'boolean',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($sub) {
            if (empty($sub->uuid)) {
                $sub->uuid = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(SubscriptionUsage::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereIn('status', [SubscriptionStatus::ACTIVE->value, SubscriptionStatus::TRIALING->value])
              ->where(function ($subQ) {
                  $subQ->whereNull('ends_at')->orWhere('ends_at', '>', now());
              });
        });
    }

    public function isActive(): bool
    {
        if ($this->status === SubscriptionStatus::ACTIVE) {
            return is_null($this->ends_at) || $this->ends_at->isFuture();
        }

        if ($this->status === SubscriptionStatus::TRIALING) {
            return is_null($this->trial_ends_at) || $this->trial_ends_at->isFuture();
        }

        return false;
    }

    public function onTrial(): bool
    {
        return $this->status === SubscriptionStatus::TRIALING && (!is_null($this->trial_ends_at) && $this->trial_ends_at->isFuture());
    }

    public function canceled(): bool
    {
        return !is_null($this->canceled_at);
    }

    public function onGracePeriod(): bool
    {
        return $this->canceled() && !is_null($this->ends_at) && $this->ends_at->isFuture();
    }

    public function onPaymentSuccess(Payment $payment): void
    {
        $this->update([
            'status' => SubscriptionStatus::ACTIVE,
            'payment_method' => $payment->gateway,
        ]);

        // Update user status
        $this->user?->update([
            'is_subscribed' => true,
            'subscription_ends_at' => $this->ends_at,
        ]);
    }

    public function onPaymentFailed(Payment $payment): void
    {
        $this->update([
            'status' => SubscriptionStatus::PAST_DUE,
        ]);
    }
}
