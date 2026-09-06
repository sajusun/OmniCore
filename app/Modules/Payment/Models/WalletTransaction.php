<?php

namespace App\Modules\Payment\Models;

use App\Models\User;
use App\Modules\Payment\Enums\TransactionType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'wallet_id',
        'user_id',
        'type',
        'amount',
        'before_balance',
        'after_balance',
        'fee',
        'currency',
        'reference_type',
        'reference_id',
        'description',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'before_balance' => 'decimal:2',
        'after_balance' => 'decimal:2',
        'fee' => 'decimal:2',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($trx) {
            if (empty($trx->uuid)) {
                $trx->uuid = (string) Str::uuid();
            }
        });
    }

    public function trxId(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->uuid,
            set: fn ($value) => ['uuid' => $value]
        );
    }

    public function balanceBefore(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->before_balance,
            set: fn ($value) => ['before_balance' => $value]
        );
    }

    public function balanceAfter(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->after_balance,
            set: fn ($value) => ['after_balance' => $value]
        );
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
