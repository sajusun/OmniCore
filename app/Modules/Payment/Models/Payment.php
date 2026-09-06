<?php

namespace App\Modules\Payment\Models;

use App\Models\User;
use App\Modules\Payment\Enums\PaymentMethod;
use App\Modules\Payment\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'user_id',
        'payable_type',
        'payable_id',
        'gateway',
        'amount',
        'fee',
        'currency',
        'status',
        'gateway_transaction_id',
        'gateway_reference',
        'checkout_url',
        'return_url',
        'cancel_url',
        'failure_reason',
        'gateway_response',
        'metadata',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'gateway_response' => 'array',
        'metadata' => 'array',
        'paid_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($payment) {
            if (empty($payment->payment_id)) {
                $payment->payment_id = (string) Str::uuid();
            }
        });
    }

    public function method(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->gateway ? PaymentMethod::tryFrom($this->gateway) : null,
            set: fn ($value) => ['gateway' => $value instanceof PaymentMethod ? $value->value : (string) $value]
        );
    }

    public function paymentUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->checkout_url,
            set: fn ($value) => ['checkout_url' => $value]
        );
    }

    public function totalAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => round((float) $this->amount + (float) $this->fee, 2)
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', PaymentStatus::COMPLETED->value);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', PaymentStatus::PENDING->value);
    }

    public function isCompleted(): bool
    {
        return ($this->status?->value ?? $this->status) === PaymentStatus::COMPLETED->value;
    }

    public function isPending(): bool
    {
        return ($this->status?->value ?? $this->status) === PaymentStatus::PENDING->value;
    }
}
