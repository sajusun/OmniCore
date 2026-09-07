<?php

use App\Modules\Vendor\Http\Controllers\Admin\VendorAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified'])->prefix('admin/vendor')->name('admin.vendor.')->group(function () {
    Route::get('/stores', [VendorAdminController::class, 'stores'])->name('stores.index');
    Route::put('/stores/{id}', [VendorAdminController::class, 'updateStore'])->name('stores.update');
    Route::get('/payouts', [VendorAdminController::class, 'payouts'])->name('payouts.index');
    Route::post('/payouts/{id}/approve', [VendorAdminController::class, 'approvePayout'])->name('payouts.approve');
    Route::post('/payouts/{id}/reject', [VendorAdminController::class, 'rejectPayout'])->name('payouts.reject');
});
