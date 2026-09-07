<?php

use App\Modules\Reward\Http\Controllers\Admin\RewardAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified'])->prefix('admin/reward')->name('admin.reward.')->group(function () {
    Route::get('/tiers', [RewardAdminController::class, 'tiers'])->name('tiers.index');
    Route::put('/tiers/{id}', [RewardAdminController::class, 'updateTier'])->name('tiers.update');
    Route::get('/badges', [RewardAdminController::class, 'badges'])->name('badges.index');
    Route::post('/badges', [RewardAdminController::class, 'storeBadge'])->name('badges.store');
    Route::delete('/badges/{id}', [RewardAdminController::class, 'deleteBadge'])->name('badges.destroy');
    Route::get('/leaderboard', [RewardAdminController::class, 'leaderboard'])->name('leaderboard.index');
});
