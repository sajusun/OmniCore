<?php

use App\Modules\Vendor\Controllers\Admin\VendorAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware('permission:vendor.list|vendor.edit')->prefix('vendors')->name('vendors.')->group(function () {
    Route::get('/', [VendorAdminController::class, 'index'])->name('index');
    Route::put('/{store}', [VendorAdminController::class, 'update'])->name('update');
    Route::get('/payouts', [VendorAdminController::class, 'payouts'])->name('payouts');
    Route::patch('/payouts/{payout}', [VendorAdminController::class, 'processPayout'])->name('payouts.process');
});
