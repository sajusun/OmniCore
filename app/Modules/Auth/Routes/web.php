<?php

use App\Modules\Auth\Http\Controllers\Web\AuthenticatedSessionController;
use App\Modules\Auth\Http\Controllers\Web\ConfirmablePasswordController;
use App\Modules\Auth\Http\Controllers\Web\EmailVerificationNotificationController;
use App\Modules\Auth\Http\Controllers\Web\EmailVerificationPromptController;
use App\Modules\Auth\Http\Controllers\Web\NewPasswordController;
use App\Modules\Auth\Http\Controllers\Web\PasswordController;
use App\Modules\Auth\Http\Controllers\Web\PasswordResetLinkController;
use App\Modules\Auth\Http\Controllers\Web\RegisteredUserController;
use App\Modules\Auth\Http\Controllers\Web\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('check')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store'])->middleware('throttle:auth-limiter');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:auth-limiter');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email')->middleware('throttle:auth-limiter');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store')->middleware('throttle:auth-limiter');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])->middleware('throttle:auth-limiter');

    Route::put('password', [PasswordController::class, 'update'])->name('password.update')->middleware('throttle:auth-limiter');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::middleware(['check'])->group(function () {
    Route::get('verify/otp/page', [RegisteredUserController::class, 'otpPage'])->name('verify.otp.page');
    Route::post('verify/otp', [RegisteredUserController::class, 'otpVerify'])->name('verify.otp')->middleware('throttle:auth-limiter');
    Route::get('verify/otp/resend/page', [RegisteredUserController::class, 'otpResendPage'])->name('verify.otp.resend.page');
    Route::post('verify/otp/resend', [RegisteredUserController::class, 'otpResend'])->name('verify.otp.resend')->middleware('throttle:otp-limiter');
});
