<?php

use App\Modules\Order\Http\Controllers\Admin\AdminOrderAnalyticsController;
use App\Modules\Order\Http\Controllers\Admin\AdminOrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Orders & Analytics API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1/admin')->name('admin.')->middleware(['auth:api'])->group(function () {
    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::put('/orders/{id}/payment', [AdminOrderController::class, 'updatePaymentStatus'])->name('orders.payment');

    // E-Commerce Analytics Dashboard
    Route::get('/analytics/ecommerce', [AdminOrderAnalyticsController::class, 'index'])->name('analytics.ecommerce');
});
