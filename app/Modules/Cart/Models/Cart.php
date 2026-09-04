<?php

namespace App\Modules\Cart\Models;

use App\Models\User;
use App\Modules\Coupon\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';

    protected $fillable = [
        'user_id',
        'guest_token',
        'coupon_code',
        'discount_amount',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }

    /**
     * Get subtotal of all items in cart
     */
    public function getSubtotalAttribute(): float
    {
        return (float) $this->items->sum(fn ($item) => $item->subtotal);
    }

    /**
     * Get total item count
     */
    public function getTotalItemsCountAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }

    /**
     * Calculate grand total (subtotal - discount)
     */
    public function getGrandTotalAttribute(): float
    {
        $subtotal = $this->subtotal;
        $discount = (float) $this->discount_amount;

        return max(0.00, round($subtotal - $discount, 2));
    }
}
