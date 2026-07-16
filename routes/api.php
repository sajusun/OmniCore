<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\FoodLogController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\FoodScanController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\FoodScannerController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\FirebaseTokenController;
use App\Http\Controllers\Api\NutritionGoalController;
use App\Http\Controllers\Api\Insights\MacrosController;
use App\Http\Controllers\Api\Auth\SocialLoginController;
use App\Http\Controllers\Api\SubscriptionPlanController;
use App\Http\Controllers\Api\Insights\CaloriesController;
use App\Http\Controllers\Api\RevenueCatWebhookController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Insights\DashboardController;
use App\Http\Controllers\Api\Insights\FoodScoreController;

Route::group(['middleware' => 'guest:api'], function ($router) {
    //register
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('/verify-email', [RegisterController::class, 'VerifyEmail']);
    Route::post('/resend-otp', [RegisterController::class, 'ResendOtp']);
    Route::post('/verify-otp', [RegisterController::class, 'VerifyEmail']);
    //login
    Route::post('login', [LoginController::class, 'login'])->name('api.login');

    //forgot password
    Route::post('/forget-password', [ResetPasswordController::class, 'forgotPassword']);
    Route::post('/otp-token', [ResetPasswordController::class, 'MakeOtpToken']);
    Route::post('/reset-password', [ResetPasswordController::class, 'ResetPassword']);
    //social login
    Route::post('/social-login/{provider}', [SocialLoginController::class, 'SocialLogin']);
    // Route::post('/social-login/update-user-type', [SocialLoginController::class, 'UpdateUserType']); // New route for updating user type after social login
});


Route::group(['middleware' => 'auth:api'], function ($router) {
    Route::get('/refresh-token', [LoginController::class, 'refreshToken']);

    Route::post('/logout', [LogoutController::class, 'logout']);
    Route::get('/me', [UserController::class, 'me']);
    Route::post('/update-profile', [UserController::class, 'updateProfile']);
    Route::post('/update-avatar', [UserController::class, 'updateAvatar']);
    Route::delete('/delete-profile', [UserController::class, 'destroy']);

    // Food scan endpoints
    Route::post('/food/scan', [FoodScanController::class, 'store']);
    Route::get('/food/scans', [FoodScanController::class, 'index']);
    Route::get('/food/summary/today', [FoodScanController::class, 'todaySummary']);
    Route::post('/food/scan/{id}/confirm', [FoodScanController::class, 'confirm']);
});


// Ojais Wellness Food Scanning
Route::middleware(['auth:api'])->prefix('ojais')->group(function () {
    Route::post('/scan-food', [FoodScannerController::class, 'scanImage']);
    Route::get('/scan-food/{id}', [FoodScannerController::class, 'getFoodScan']);
    Route::get('/scan-history', [FoodScannerController::class, 'scanHistory']);
});


Route::middleware(['auth:api'])->prefix('ojais')->group(function () {
    // Food Logs (The Diary)
    Route::post('/food/log/store', [FoodLogController::class, 'store']);
    // update food log
    Route::post('/food/log/update', [FoodLogController::class, 'update']);
    Route::get('/food/log/show', [FoodLogController::class, 'index']);
    Route::delete('/food/log/delete', [FoodLogController::class, 'destroy']);

    // Insights Engine
    Route::get('/insights/dashboard', [DashboardController::class, 'index']);


    
    Route::get('/insights/calories/details', [CaloriesController::class, 'index']);
    Route::get('/insights/macros/details', [MacrosController::class, 'index']);
    Route::get('/insights/food-scores', [FoodScoreController::class, 'index']);
});

Route::middleware(['auth:api'])->prefix('products')->group(function () {
    Route::get('/search', [ProductController::class, 'search']);
});









/*
# Firebase Notification Route
*/

Route::middleware(['auth:api'])->controller(FirebaseTokenController::class)->prefix('firebase')->group(function () {
    Route::get("test", "test");
    Route::post("token/add", "store");
    Route::post("token/get", "getToken");
    Route::post("token/delete", "deleteToken");
});


Route::prefix('cms')->name('cms.')->group(function () {
    Route::get('/{name}/{section}', [PageController::class, 'index'])->name('pages');
});

Route::get('/subscription-plans', [SubscriptionPlanController::class, 'index']);
Route::post('/webhooks/revenuecat', [RevenueCatWebhookController::class, 'handleWebhook']);


Route::middleware('auth:api')->group(function () {
    Route::get('/nutrition-goal', [NutritionGoalController::class, 'show']);
    Route::post('/nutrition-goal', [NutritionGoalController::class, 'storeOrUpdate']);
});


Route::middleware('auth:api')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications', [NotificationController::class, 'store']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});



Route::get('/cache_clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('optimize:clear');
    return response()->json(['message' => 'Cache cleared.']);
});

Route::post('app/webhooks/revenuecat', RevenueCatWebhookController::class);





// others loaded routes
require app_path('Modules/Media/Routes/media.php');
