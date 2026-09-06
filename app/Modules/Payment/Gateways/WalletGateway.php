<?php

namespace App\Modules\Payment\Gateways;

use App\Modules\Payment\Enums\PaymentStatus;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Services\WalletService;
use Illuminate\Support\Facades\Log;

class WalletGateway extends BaseGateway
{
    public function charge(Payment $payment, array $options = []): array
    {
        $user = $payment->user;
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found for wallet payment',
            ];
        }

        try {
            $walletService = app(WalletService::class);
            $wallet = $walletService->getOrCreateWallet($user);

            if (!$wallet->canTransact()) {
                return [
                    'success' => false,
                    'message' => 'Wallet is currently inactive or frozen',
                ];
            }

            if (!$wallet->hasSufficientBalance($payment->total_amount)) {
                return [
                    'success' => false,
                    'message' => 'Insufficient wallet balance',
                ];
            }

            $trx = $walletService->pay(
                user: $user,
                amount: (float) $payment->total_amount,
                reference: $payment->payable,
                description: 'Payment for ' . class_basename($payment->payable_type) . ' #' . $payment->payable_id
            );

            return [
                'success' => true,
                'status' => PaymentStatus::COMPLETED->value,
                'transaction_id' => $trx->trx_id,
                'message' => 'Payment deducted from wallet successfully',
            ];
        } catch (\Throwable $e) {
            Log::error('WalletGateway charge error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function verify(Payment $payment, array $payload = []): array
    {
        return [
            'success' => $payment->isCompleted(),
            'status' => $payment->status->value,
            'transaction_id' => $payment->gateway_transaction_id,
            'message' => $payment->isCompleted() ? 'Wallet payment verified' : 'Payment pending/failed',
        ];
    }

    public function refund(Payment $payment, ?float $amount = null, string $reason = ''): array
    {
        $user = $payment->user;
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found for wallet refund',
            ];
        }

        try {
            $refundAmount = $amount ?? (float) $payment->total_amount;
            $walletService = app(WalletService::class);
            $trx = $walletService->refund(
                user: $user,
                amount: $refundAmount,
                reference: $payment->payable,
                description: 'Refund for Payment #' . $payment->payment_id . ($reason ? " ({$reason})" : '')
            );

            return [
                'success' => true,
                'refund_id' => $trx->trx_id,
                'message' => 'Refund credited back to wallet',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
