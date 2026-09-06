<?php

use App\Modules\Reward\Http\Controllers\Api\RewardApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Reward, Loyalty & Gamification API Routes
|--------------------------------------------------------------------------
*/

// Public Tiers & Leaderboard
Route::prefix('rewards')->group(function () {
    Route::get('tiers', [RewardApiController::class, 'tiers']);
    Route::get('leaderboard', [RewardApiController::class, 'leaderboard']);
});

// Authenticated User Points, Daily Check-in, Redemption
Route::middleware('auth:api')->prefix('rewards')->group(function () {
    Route::get('/', [RewardApiController::class, 'index']);
    Route::post('checkin', [RewardApiController::class, 'checkin']);
    Route::post('redeem', [RewardApiController::class, 'redeem']);
    Route::get('history', [RewardApiController::class, 'history']);
    Route::get('badges', [RewardApiController::class, 'badges']);
});
