<?php

namespace App\Modules\Coupon\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'coupons';

    protected $fillable = [
        'code',
        'type', // percentage, fixed
        'value',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit',
        'usage_limit_per_user',
        'times_used',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_limit_per_user' => 'integer',
        'times_used' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class, 'coupon_id');
    }

    /**
     * Validate coupon eligibility for a user and subtotal
     */
    public function isValid(?User $user, float $subtotal): array
    {
        if (! $this->is_active) {
            return ['valid' => false, 'message' => 'This coupon is no longer active.'];
        }

        $now = now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return ['valid' => false, 'message' => 'This coupon is not active yet.'];
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return ['valid' => false, 'message' => 'This coupon has expired.'];
        }

        if ($this->usage_limit && $this->times_used >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'Coupon usage limit has been reached.'];
        }

        if ($this->min_order_amount && $subtotal < $this->min_order_amount) {
            return [
                'valid' => false,
                'message' => "Minimum order amount of \${$this->min_order_amount} required to use this coupon.",
            ];
        }

        if ($user && $this->usage_limit_per_user) {
            $userUsageCount = $this->usages()->where('user_id', $user->id)->count();
            if ($userUsageCount >= $this->usage_limit_per_user) {
                return ['valid' => false, 'message' => 'You have already used this coupon the maximum allowed times.'];
            }
        }

        return ['valid' => true, 'message' => 'Coupon applied successfully.'];
    }

    /**
     * Calculate discount amount given a subtotal
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'percentage') {
            $discount = ($subtotal * $this->value) / 100;
            if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
                $discount = (float) $this->max_discount_amount;
            }

            return round(min($subtotal, $discount), 2);
        }

        // Fixed amount discount
        return round(min($subtotal, (float) $this->value), 2);
    }
}
