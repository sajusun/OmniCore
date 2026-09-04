<?php

namespace App\Modules\Order\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $table = 'shipping_methods';

    protected $fillable = [
        'name',
        'code',
        'cost',
        'free_shipping_threshold',
        'estimated_delivery_days',
        'description',
        'is_active',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Calculate final shipping cost given an order subtotal
     */
    public function calculateCost(float $subtotal): float
    {
        if ($this->free_shipping_threshold && $subtotal >= $this->free_shipping_threshold) {
            return 0.00;
        }

        return (float) $this->cost;
    }
}
