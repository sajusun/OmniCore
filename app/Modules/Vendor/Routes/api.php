<?php

use App\Modules\Vendor\Controllers\Api\VendorApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/vendors')->group(function () {
    // Public directory
    Route::get('/', [VendorApiController::class, 'index'])->name('api.vendors.index');
    Route::get('/store/{slug}', [VendorApiController::class, 'show'])->name('api.vendors.show');

    // Authenticated vendor endpoints
    Route::middleware(['auth:api'])->group(function () {
        Route::get('/my-store', [VendorApiController::class, 'myStore'])->name('api.vendors.my_store');
        Route::post('/register', [VendorApiController::class, 'register'])->name('api.vendors.register');
        Route::post('/update', [VendorApiController::class, 'update'])->name('api.vendors.update');
        Route::get('/payouts', [VendorApiController::class, 'payouts'])->name('api.vendors.payouts');
        Route::post('/payouts/request', [VendorApiController::class, 'requestPayout'])->name('api.vendors.payouts.request');
    });
});
