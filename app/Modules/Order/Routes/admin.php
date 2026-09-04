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
    Route::get('/{id}', [OrderController::class, 'show'])->name('show');
    Route::post('/{id}/update-status', [OrderController::class, 'updateStatus'])->name('update-status');
    Route::get('/{id}/invoice', [OrderController::class, 'invoice'])->name('invoice');
    Route::delete('/{id}', [OrderController::class, 'destroy'])->name('destroy');
});

Route::prefix('analytics')->name('analytics.')->group(function () {
    Route::get('/ecommerce', [OrderAnalyticsController::class, 'index'])->name('ecommerce');
});
