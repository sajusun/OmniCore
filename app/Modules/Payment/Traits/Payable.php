<?php

namespace App\Modules\Payment\Traits;

use App\Modules\Payment\Models\Payment;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait Payable
{
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable')->latest('id');
    }

    public function latestPayment(): MorphOne
    {
        return $this->morphOne(Payment::class, 'payable')->latestOfMany();
    }

    public function isPaid(): bool
    {
        return $this->payments()->where('status', 'completed')->exists();
    }
}
