<?php

namespace App\Modules\Coupon\Services;

use App\Models\User;
use App\Modules\Coupon\Models\Coupon;
use App\Modules\Coupon\Models\CouponUsage;
use App\Modules\Order\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class CouponService
{
    /**
     * List coupons for admin
     */
    public function getAdminCoupons(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Coupon::query();

        if (isset($filters['is_active'])) {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        if (! empty($filters['search'])) {
            $query->where('code', 'like', '%'.$filters['search'].'%');
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create coupon
     */
    public function createCoupon(array $data): Coupon
    {
        $data['code'] = Str::upper(trim($data['code']));

        return Coupon::create($data);
    }

    /**
     * Update coupon
     */
    public function updateCoupon(Coupon $coupon, array $data): Coupon
    {
        if (isset($data['code'])) {
            $data['code'] = Str::upper(trim($data['code']));
        }
        $coupon->update($data);

        return $coupon;
    }

    /**
     * Record coupon usage on order placement
     */
    public function recordUsage(Coupon $coupon, User $user, Order $order, float $discountAmount): CouponUsage
    {
        $coupon->increment('times_used');

        return CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'order_id' => $order->id,
            'discount_amount' => $discountAmount,
        ]);
    }
}
