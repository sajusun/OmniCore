<?php

use App\Modules\Payment\Http\Controllers\Backend\GatewaySettingController;
use App\Modules\Payment\Http\Controllers\Backend\PaymentHistoryController;
use App\Modules\Payment\Http\Controllers\Backend\WalletManagementController;
use App\Modules\Payment\Http\Controllers\Backend\WithdrawalAdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Payment & Wallet Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('/', [PaymentHistoryController::class, 'index'])->name('index');
    Route::get('/{payment}', [PaymentHistoryController::class, 'show'])->name('show');
    Route::post('/{payment}/mark-paid', [PaymentHistoryController::class, 'markPaid'])->name('mark-paid');
    Route::post('/{payment}/refund', [PaymentHistoryController::class, 'refund'])->name('refund');
});

Route::prefix('wallets')->name('wallets.')->group(function () {
    Route::get('/', [WalletManagementController::class, 'index'])->name('index');
    Route::get('/transactions', [WalletManagementController::class, 'transactions'])->name('transactions');
    Route::post('/{wallet}/toggle-freeze', [WalletManagementController::class, 'toggleFreeze'])->name('toggle-freeze');
    Route::post('/{wallet}/adjust-balance', [WalletManagementController::class, 'adjustBalance'])->name('adjust-balance');
});

Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
    Route::get('/', [WithdrawalAdminController::class, 'index'])->name('index');
    Route::post('/{withdrawal}/approve', [WithdrawalAdminController::class, 'approve'])->name('approve');
    Route::post('/{withdrawal}/reject', [WithdrawalAdminController::class, 'reject'])->name('reject');
});

Route::prefix('gateways')->name('gateways.')->group(function () {
    Route::get('/', [GatewaySettingController::class, 'index'])->name('index');
    Route::put('/{gateway}', [GatewaySettingController::class, 'update'])->name('update');
});
