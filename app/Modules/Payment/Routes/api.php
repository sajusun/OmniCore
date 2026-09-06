<?php

use App\Modules\Payment\Http\Controllers\Api\PaymentApiController;
use App\Modules\Payment\Http\Controllers\Api\WalletApiController;
use App\Modules\Payment\Http\Controllers\Api\WebhookController;
use App\Modules\Payment\Http\Controllers\Api\WithdrawalApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Payment & Wallet API Routes
|--------------------------------------------------------------------------
*/

// Public / Gateway Callbacks & Webhooks
Route::prefix('payments')->group(function () {
    Route::get('gateways', [PaymentApiController::class, 'gateways']);
    Route::match(['get', 'post'], 'verify/{paymentId}', [PaymentApiController::class, 'verify'])->name('payments.verify');
    Route::match(['get', 'post'], 'cancel/{paymentId}', [PaymentApiController::class, 'cancel'])->name('payments.cancel');
    Route::post('webhooks/{gateway}', [WebhookController::class, 'handle'])->name('payments.webhook');
});

// Authenticated User Payment & Wallet Endpoints
Route::middleware('auth:api')->group(function () {
    // Payment History & Initiation
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentApiController::class, 'index']);
        Route::get('{paymentId}', [PaymentApiController::class, 'show']);
        Route::post('initiate', [PaymentApiController::class, 'initiate']);
    });

    // In-App Wallet
    Route::prefix('wallet')->group(function () {
        Route::get('/', [WalletApiController::class, 'balance']);
        Route::get('transactions', [WalletApiController::class, 'transactions']);
        Route::post('deposit', [WalletApiController::class, 'deposit']);
        Route::post('transfer', [WalletApiController::class, 'transfer']);
    });

    // Withdrawals
    Route::prefix('withdrawals')->group(function () {
        Route::get('/', [WithdrawalApiController::class, 'index']);
        Route::post('/', [WithdrawalApiController::class, 'store']);
        Route::post('{id}/cancel', [WithdrawalApiController::class, 'cancel']);
    });
});
