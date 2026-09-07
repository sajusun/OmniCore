<?php

namespace App\Modules\Coupon\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Coupon\Http\Resources\CouponResource;
use App\Modules\Coupon\Models\Coupon;
use App\Modules\Coupon\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminCouponController extends Controller
{
    public function __construct(protected CouponService $couponService) {}

    public function index(Request $request): JsonResponse
    {
        $coupons = $this->couponService->getAdminCoupons(
            $request->all(),
            (int) $request->get('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'data' => CouponResource::collection($coupons),
            'meta' => [
                'current_page' => $coupons->currentPage(),
                'last_page' => $coupons->lastPage(),
                'total' => $coupons->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        $coupon = $this->couponService->createCoupon($validated);

        return response()->json([
            'success' => true,
            'message' => 'Coupon created successfully.',
            'data' => new CouponResource($coupon),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $coupon = Coupon::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new CouponResource($coupon),
        ]);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $coupon = Coupon::findOrFail($id);

        $validated = $request->validate([
            'code' => 'sometimes|required|string|max:50|unique:coupons,code,'.$coupon->id,
            'type' => 'sometimes|required|in:percentage,fixed',
            'value' => 'sometimes|required|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $updatedCoupon = $this->couponService->updateCoupon($coupon, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Coupon updated successfully.',
            'data' => new CouponResource($updatedCoupon),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Coupon deleted successfully.',
        ]);
    }
}
