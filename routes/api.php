<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Frontend\CmsController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\FirebaseTokenController;
use App\Http\Controllers\Api\Auth\SocialLoginController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Webhooks\RevenueCatWebhookController;

Route::group(['middleware' => 'guest:api'], function ($router) {

    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/register/verify', [RegisterController::class, 'VerifyEmail']);
    Route::post('/otp/resend', [RegisterController::class, 'ResendOtp']);
    Route::post('/otp/verify', [RegisterController::class, 'VerifyEmail']);

    Route::post('login', [LoginController::class, 'login'])->name('api.login');
    Route::post('/forget-password', [ResetPasswordController::class, 'forgotPassword']);
    Route::post('/forget-password/token', [ResetPasswordController::class, 'resetSecretKey']);
    Route::post('/reset-password', [ResetPasswordController::class, 'ResetPassword']);
    // social login
    Route::post('/social-login', [SocialLoginController::class, 'SocialLogin']);
});

Route::group(['middleware' => 'auth:api'], function ($router) {
    Route::get('/refresh-token', [LoginController::class, 'refreshToken']);
    Route::post('/logout', [LogoutController::class, 'logout']);
    Route::get('/me', [UserController::class, 'me']);
    Route::post('/update-profile', [UserController::class, 'updateProfile']);
    Route::post('/onboarding-fill', [UserController::class, 'onboardingUpdate']);
    Route::post('/update-avatar', [UserController::class, 'updateAvatar']);
    Route::delete('/profile/delete', [UserController::class, 'destroy']);
});

/*
# Firebase Notification Route
*/

Route::middleware(['auth:api'])->controller(FirebaseTokenController::class)->prefix('firebase')->group(function () {
    Route::post('firebase-token', 'store');
    Route::post('firebase-token/delete', 'destroy');
    Route::post('firebase-token/touch', 'touch');
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
    });
});

// subscribe newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);
Route::post('/contact-us', [ContactController::class, 'store']);

Route::prefix('cms')->name('cms.')->group(function () {
    Route::get('/', [CmsController::class, 'index'])->name('index');   // All pages & their sections
    Route::get('{page}', [CmsController::class, 'page'])->name('page');    // All sections of a page
    Route::get('{page}/{section}', [CmsController::class, 'section'])->name('section'); // Single section
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

// others loaded routes
require app_path('Modules/Media/Routes/api.php');

// Public – token link click (no auth required, user clicks from email)
Route::get('/verification/verify-token', [VerificationController::class, 'verifyToken'])->name('verification.token.verify');

// Protected – authenticated user actions
Route::middleware(['auth:api'])->prefix('verification')->name('api.verification.')->controller(VerificationController::class)->group(function () {
    Route::post('send', 'send')->name('send');
    Route::post('verify-otp', 'verifyOtp')->name('verify-otp');
    Route::post('resend', 'resend')->name('resend');
});

Route::post('app/webhooks/revenuecat', RevenueCatWebhookController::class);
