<?php

use App\Modules\Affiliate\Controllers\Admin\AffiliateAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware('permission:affiliate.list|affiliate.edit')->prefix('affiliates')->name('affiliates.')->group(function () {
    Route::get('/', [AffiliateAdminController::class, 'index'])->name('index');
    Route::put('/{account}', [AffiliateAdminController::class, 'update'])->name('update');
    Route::get('/commissions', [AffiliateAdminController::class, 'commissions'])->name('commissions');
});
