<?php

use App\Modules\Auth\Http\Controllers\Api\LoginApiController;
use App\Modules\Auth\Http\Controllers\Api\LogoutApiController;
use App\Modules\Auth\Http\Controllers\Api\RegisterApiController;
use App\Modules\Auth\Http\Controllers\Api\ResetPasswordApiController;
use App\Modules\Auth\Http\Controllers\Api\SocialLoginApiController;
use App\Modules\Auth\Http\Controllers\Api\UserApiController;
use App\Modules\Auth\Http\Controllers\Api\VerificationApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Guest Auth Routes (Throttled for Security)
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['guest:api', 'throttle:auth-limiter']], function () {
    Route::post('/register', [RegisterApiController::class, 'register']);
    Route::post('/register/verify', [RegisterApiController::class, 'verifyEmail']);
    Route::post('/otp/resend', [RegisterApiController::class, 'resendOtp'])->middleware('throttle:otp-limiter');
    Route::post('/otp/verify', [RegisterApiController::class, 'verifyEmail']);

    Route::post('/login', [LoginApiController::class, 'login'])->name('api.login');
    Route::post('/forget-password', [ResetPasswordApiController::class, 'forgotPassword']);
    Route::post('/forget-password/token', [ResetPasswordApiController::class, 'resetSecretKey']);
    Route::post('/reset-password', [ResetPasswordApiController::class, 'resetPassword']);
    Route::post('/social-login', [SocialLoginApiController::class, 'socialLogin']);
});

/*
|--------------------------------------------------------------------------
| Public Verification Token Endpoint
|--------------------------------------------------------------------------
*/
Route::get('/verification/verify-token', [VerificationApiController::class, 'verifyToken'])->name('verification.token.verify');

/*
|--------------------------------------------------------------------------
| Protected Authenticated Auth & Profile Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:api', 'throttle:api'])->group(function () {
    // ─── Session & Token ─────────────────────────────────────────────────
    Route::get('/refresh-token', [LoginApiController::class, 'refreshToken']);
    Route::post('/logout', [LogoutApiController::class, 'logout']);

    // ─── User Profile ────────────────────────────────────────────────────
    Route::get('/me', [UserApiController::class, 'me']);
    Route::get('/profile/{user}', [UserApiController::class, 'publicProfile']);
    Route::post('/update-profile', [UserApiController::class, 'updateProfile']);
    Route::post('/onboarding-fill', [UserApiController::class, 'onboardingUpdate']);
    Route::post('/update-avatar', [UserApiController::class, 'updateAvatar']);
    Route::post('/update-password', [UserApiController::class, 'updatePassword']);
    Route::delete('/profile/delete', [UserApiController::class, 'destroy']);

    // ─── Account Verification ────────────────────────────────────────────
    Route::prefix('verification')->name('api.verification.')->controller(VerificationApiController::class)->group(function () {
        Route::post('send', 'send')->name('send');
        Route::post('verify-otp', 'verifyOtp')->name('verify-otp');
        Route::post('resend', 'resend')->name('resend')->middleware('throttle:otp-limiter');
    });
});
