<?php

use App\Modules\Order\Http\Controllers\Api\CheckoutOrderController;
use App\Modules\Order\Http\Controllers\Api\ShippingMethodController;
use App\Modules\Order\Http\Controllers\Api\UserAddressController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront Order, Checkout, Address, and Shipping API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1/store')->group(function () {
    // Public Shipping Methods
    Route::get('/shipping-methods', [ShippingMethodController::class, 'index']);

    // Protected Customer Routes
    Route::middleware(['auth:api'])->group(function () {
        // Address Book
        Route::get('/addresses', [UserAddressController::class, 'index']);
        Route::post('/addresses', [UserAddressController::class, 'store']);
        Route::put('/addresses/{id}', [UserAddressController::class, 'update']);
        Route::post('/addresses/{id}/default', [UserAddressController::class, 'setDefault']);
        Route::delete('/addresses/{id}', [UserAddressController::class, 'destroy']);

        // Orders & Checkout
        Route::post('/checkout', [CheckoutOrderController::class, 'checkout']);
        Route::get('/orders', [CheckoutOrderController::class, 'index']);
        Route::get('/orders/{orderNumber}', [CheckoutOrderController::class, 'show']);
        Route::get('/orders/{orderNumber}/track', [CheckoutOrderController::class, 'track']);
        Route::post('/orders/{orderNumber}/cancel', [CheckoutOrderController::class, 'cancel']);
    });
});
