<?php

namespace App\Modules\Payment\Models;

use App\Models\User;
use App\Modules\Payment\Enums\WithdrawalStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class WithdrawalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'amount',
        'fee',
        'net_amount',
        'currency',
        'method',
        'account_details',
        'status',
        'processed_by',
        'processed_at',
        'admin_note',
        'rejection_reason',
        'transaction_proof',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'account_details' => 'array',
        'processed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($w) {
            if (empty($w->uuid)) {
                $w->uuid = (string) Str::uuid();
            }
        });
    }

    public function payableAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->net_amount,
            set: fn ($value) => ['net_amount' => $value]
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', WithdrawalStatus::PENDING->value);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', WithdrawalStatus::APPROVED->value);
    }
}
