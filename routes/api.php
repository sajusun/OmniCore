<?php

use App\Events\TestBroadcastEvent;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\SocialLoginController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\FirebaseTokenController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\Webhooks\RevenueCatWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Guest Routes (Throttled for Security)
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['guest:api', 'throttle:auth-limiter']], function () {
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/register/verify', [RegisterController::class, 'VerifyEmail']);
    Route::post('/otp/resend', [RegisterController::class, 'ResendOtp'])->middleware('throttle:otp-limiter');
    Route::post('/otp/verify', [RegisterController::class, 'VerifyEmail']);

    Route::post('/login', [LoginController::class, 'login'])->name('api.login');
    Route::post('/forget-password', [ResetPasswordController::class, 'forgotPassword']);
    Route::post('/forget-password/token', [ResetPasswordController::class, 'resetSecretKey']);
    Route::post('/reset-password', [ResetPasswordController::class, 'ResetPassword']);
    Route::post('/social-login', [SocialLoginController::class, 'SocialLogin']);
});

/*
|--------------------------------------------------------------------------
| Public General Endpoints
|--------------------------------------------------------------------------
*/
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);
Route::post('/contact-us', [ContactController::class, 'store']);
Route::get('/verification/verify-token', [VerificationController::class, 'verifyToken'])->name('verification.token.verify');
Route::post('app/webhooks/revenuecat', RevenueCatWebhookController::class);

/*
|--------------------------------------------------------------------------
| Protected Authenticated Routes (with Rate Limiting)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:api', 'throttle:api'])->group(function () {
    // ─── User Profile & Session ──────────────────────────────────────────
    Route::get('/refresh-token', [LoginController::class, 'refreshToken']);
    Route::post('/logout', [LogoutController::class, 'logout']);
    Route::get('/me', [UserController::class, 'me']);
    Route::get('/profile/{user}', [UserController::class, 'publicProfile']);
    Route::post('/update-profile', [UserController::class, 'updateProfile']);
    Route::post('/onboarding-fill', [UserController::class, 'onboardingUpdate']);
    Route::post('/update-avatar', [UserController::class, 'updateAvatar']);
    Route::post('/update-password', [UserController::class, 'updatePassword']);
    Route::delete('/profile/delete', [UserController::class, 'destroy']);

    // ─── Firebase Tokens ─────────────────────────────────────────────────
    Route::prefix('firebase')->controller(FirebaseTokenController::class)->group(function () {
        Route::post('firebase-token', 'store');
        Route::post('firebase-token/delete', 'destroy');
        Route::post('firebase-token/touch', 'touch');
    });

    // ─── Notifications ───────────────────────────────────────────────────
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/destroy-all', [NotificationController::class, 'destroyAll']);
        Route::delete('/delete-all', [NotificationController::class, 'destroyAll']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
    });

    // ─── Account Verification ───────────────────────────────────────────
    Route::prefix('verification')->name('api.verification.')->controller(VerificationController::class)->group(function () {
        Route::post('send', 'send')->name('send');
        Route::post('verify-otp', 'verifyOtp')->name('verify-otp');
        Route::post('resend', 'resend')->name('resend')->middleware('throttle:otp-limiter');
    });
});
