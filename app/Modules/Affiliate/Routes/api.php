<?php

use App\Modules\Affiliate\Controllers\Api\AffiliateApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/affiliate')->group(function () {
    // Public visit tracking
    Route::post('/track', [AffiliateApiController::class, 'track'])->name('api.affiliate.track');

    // Authenticated partner endpoints
    Route::middleware(['auth:api'])->group(function () {
        Route::get('/account', [AffiliateApiController::class, 'account'])->name('api.affiliate.account');
        Route::get('/referrals', [AffiliateApiController::class, 'referrals'])->name('api.affiliate.referrals');
        Route::get('/commissions', [AffiliateApiController::class, 'commissions'])->name('api.affiliate.commissions');
        Route::patch('/slug', [AffiliateApiController::class, 'updateSlug'])->name('api.affiliate.slug');
        Route::post('/payout', [AffiliateApiController::class, 'payout'])->name('api.affiliate.payout');
    });
});
