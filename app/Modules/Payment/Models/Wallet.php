<?php

namespace App\Modules\Payment\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'frozen_balance',
        'currency',
        'status',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'frozen_balance' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class)->latest('id');
    }

    public function isActive(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'active',
            set: fn ($value) => ['status' => $value ? 'active' : 'suspended']
        );
    }

    public function isFrozen(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'locked',
            set: fn ($value) => ['status' => $value ? 'locked' : 'active']
        );
    }

    public function hasSufficientBalance(float|int|string $amount): bool
    {
        return (float) $this->balance >= (float) $amount;
    }

    public function canTransact(): bool
    {
        return $this->status === 'active';
    }
}
