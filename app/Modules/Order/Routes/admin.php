<?php

use App\Modules\Order\Http\Controllers\Backend\OrderAnalyticsController;
use App\Modules\Order\Http\Controllers\Backend\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Order Web Admin Routes (Dashboard Web UI)
|--------------------------------------------------------------------------
| Prefix: /admin/orders, /admin/analytics
*/

Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
});

Route::prefix('analytics')->name('analytics.')->group(function () {
    Route::get('/ecommerce', [OrderAnalyticsController::class, 'index'])->name('ecommerce');
});
