<?php

namespace App\Modules\Payment\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'logo',
        'is_active',
        'is_test_mode',
        'currencies',
        'charge_fixed',
        'charge_percentage',
        'credentials',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_test_mode' => 'boolean',
        'currencies' => 'array',
        'charge_fixed' => 'decimal:2',
        'charge_percentage' => 'decimal:2',
        'credentials' => 'array',
        'sort_order' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order', 'asc');
    }

    public function isSandbox(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_test_mode,
            set: fn ($value) => ['is_test_mode' => $value]
        );
    }

    public function feeFixed(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->charge_fixed,
            set: fn ($value) => ['charge_fixed' => $value]
        );
    }

    public function feePercent(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->charge_percentage,
            set: fn ($value) => ['charge_percentage' => $value]
        );
    }

    public function calculateFee(float|int|string $amount): float
    {
        $amount = (float) $amount;
        $fixed = (float) $this->charge_fixed;
        $percent = (float) $this->charge_percentage;

        return round($fixed + ($amount * ($percent / 100)), 2);
    }
}
