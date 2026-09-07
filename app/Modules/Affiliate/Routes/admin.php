<?php

use App\Modules\Affiliate\Http\Controllers\Admin\AffiliateAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified'])->prefix('admin/affiliate')->name('admin.affiliate.')->group(function () {
    Route::get('/', [AffiliateAdminController::class, 'index'])->name('index');
    Route::put('/{id}', [AffiliateAdminController::class, 'update'])->name('update');
    Route::get('/commissions', [AffiliateAdminController::class, 'commissions'])->name('commissions.index');
});
