<?php

use App\Modules\Reward\Http\Controllers\Backend\BadgeAdminController;
use App\Modules\Reward\Http\Controllers\Backend\TierAdminController;
use App\Modules\Reward\Http\Controllers\Backend\UserRewardAdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Reward, Loyalty & Gamification Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('tiers')->name('tiers.')->group(function () {
    Route::get('/', [TierAdminController::class, 'index'])->name('index');
    Route::get('/create', [TierAdminController::class, 'create'])->name('create');
    Route::post('/', [TierAdminController::class, 'store'])->name('store');
    Route::get('/{tier}/edit', [TierAdminController::class, 'edit'])->name('edit');
    Route::put('/{tier}', [TierAdminController::class, 'update'])->name('update');
    Route::delete('/{tier}', [TierAdminController::class, 'destroy'])->name('destroy');
});

Route::prefix('badges')->name('badges.')->group(function () {
    Route::get('/', [BadgeAdminController::class, 'index'])->name('index');
    Route::get('/create', [BadgeAdminController::class, 'create'])->name('create');
    Route::post('/', [BadgeAdminController::class, 'store'])->name('store');
    Route::get('/{badge}/edit', [BadgeAdminController::class, 'edit'])->name('edit');
    Route::put('/{badge}', [BadgeAdminController::class, 'update'])->name('update');
    Route::delete('/{badge}', [BadgeAdminController::class, 'destroy'])->name('destroy');
});

Route::prefix('rewards')->name('rewards.')->group(function () {
    Route::get('/users', [UserRewardAdminController::class, 'index'])->name('users');
    Route::post('/users/{user}/adjust', [UserRewardAdminController::class, 'adjust'])->name('adjust');
});
