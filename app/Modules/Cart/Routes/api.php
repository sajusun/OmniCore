<?php

use App\Modules\Cart\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Cart API Routes
|--------------------------------------------------------------------------
| Prefix: /api/v1/store/cart
*/

Route::prefix('v1/store/cart')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/items', [CartController::class, 'addItem']);
    Route::put('/items/{itemId}', [CartController::class, 'updateItem']);
    Route::delete('/items/{itemId}', [CartController::class, 'removeItem']);
    Route::delete('/', [CartController::class, 'clear']);
    Route::post('/apply-coupon', [CartController::class, 'applyCoupon']);
    Route::post('/remove-coupon', [CartController::class, 'removeCoupon']);

    Route::middleware(['auth:api'])->group(function () {
        Route::post('/sync', [CartController::class, 'sync']);
    });
});
