<?php

namespace App\Modules\Payment\Traits;

use App\Modules\Payment\Models\Wallet;
use App\Modules\Payment\Models\WalletTransaction;
use App\Modules\Payment\Services\WalletService;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasWallet
{
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class)->latest('id');
    }

    public function getWalletAttribute(): Wallet
    {
        return app(WalletService::class)->getOrCreateWallet($this);
    }

    public function getWalletBalanceAttribute(): float
    {
        return (float) ($this->wallet?->balance ?? 0.00);
    }
}
