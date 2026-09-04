<?php

use App\Modules\Coupon\Http\Controllers\Admin\AdminCouponController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Coupon Admin API Routes
|--------------------------------------------------------------------------
| Prefix: /api/v1/admin/coupons
*/

Route::prefix('v1/admin/coupons')->name('admin.coupons.')->middleware(['auth:api'])->group(function () {
    Route::get('/', [AdminCouponController::class, 'index'])->name('index');
    Route::post('/', [AdminCouponController::class, 'store'])->name('store');
    Route::get('/{id}', [AdminCouponController::class, 'show'])->name('show');
    Route::put('/{id}', [AdminCouponController::class, 'update'])->name('update');
    Route::delete('/{id}', [AdminCouponController::class, 'destroy'])->name('destroy');
});
