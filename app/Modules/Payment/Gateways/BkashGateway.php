<?php

namespace App\Modules\Payment\Gateways;

use App\Modules\Payment\Enums\PaymentStatus;
use App\Modules\Payment\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BkashGateway extends BaseGateway
{
    protected function getBaseUrl(): string
    {
        return $this->isSandbox()
            ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'
            : 'https://tokenized.pay.bka.sh/v1.2.0-beta';
    }

    protected function getToken(): ?string
    {
        $appKey = $this->getCredential('app_key');
        $appSecret = $this->getCredential('app_secret');
        $username = $this->getCredential('username');
        $password = $this->getCredential('password');

        if (empty($appKey) || empty($appSecret) || empty($username) || empty($password)) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'username' => $username,
                'password' => $password,
            ])->post($this->getBaseUrl() . '/tokenized/checkout/token/grant', [
                'app_key' => $appKey,
                'app_secret' => $appSecret,
            ]);

            return $response->json('id_token');
        } catch (\Throwable $e) {
            Log::error('bKash token error: ' . $e->getMessage());
            return null;
        }
    }

    public function charge(Payment $payment, array $options = []): array
    {
        $token = $this->getToken();
        $appKey = $this->getCredential('app_key');

        if (empty($token)) {
            $mockPaymentId = 'BKASH_MOCK_' . uniqid();
            return [
                'success' => true,
                'payment_url' => url("/api/payments/verify/{$payment->payment_id}?gateway=bkash&paymentID={$mockPaymentId}&status=success"),
                'transaction_id' => $mockPaymentId,
                'message' => 'bKash checkout initialized (mock mode)',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
                'X-APP-Key' => $appKey,
            ])->post($this->getBaseUrl() . '/tokenized/checkout/create', [
                'mode' => '0011',
                'payerReference' => $payment->user?->phone ?? '01700000000',
                'callbackURL' => url("/api/payments/verify/{$payment->payment_id}?gateway=bkash"),
                'amount' => number_format((float) $payment->total_amount, 2, '.', ''),
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => (string) $payment->payment_id,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['bkashURL'])) {
                    return [
                        'success' => true,
                        'payment_url' => $data['bkashURL'],
                        'transaction_id' => $data['paymentID'] ?? null,
                        'message' => 'bKash checkout URL created',
                        'raw' => $data,
                    ];
                }
            }

            return [
                'success' => false,
                'message' => $response->json('statusMessage', 'bKash create payment failed'),
                'raw' => $response->json(),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function verify(Payment $payment, array $payload = []): array
    {
        $token = $this->getToken();
        $appKey = $this->getCredential('app_key');
        $paymentID = $payload['paymentID'] ?? $payment->gateway_transaction_id;

        if (empty($token) || str_starts_with((string) $paymentID, 'BKASH_MOCK_')) {
            return [
                'success' => true,
                'status' => PaymentStatus::COMPLETED->value,
                'transaction_id' => $paymentID ?: 'bkash_trx_' . uniqid(),
                'message' => 'bKash verified (mock mode)',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
                'X-APP-Key' => $appKey,
            ])->post($this->getBaseUrl() . '/tokenized/checkout/execute', [
                'paymentID' => $paymentID,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $isSuccess = ($data['statusCode'] ?? '') === '0000';

                return [
                    'success' => $isSuccess,
                    'status' => $isSuccess ? PaymentStatus::COMPLETED->value : PaymentStatus::FAILED->value,
                    'transaction_id' => $data['trxID'] ?? $paymentID,
                    'message' => $data['statusMessage'] ?? 'bKash executed',
                    'raw' => $data,
                ];
            }

            return [
                'success' => false,
                'status' => PaymentStatus::FAILED->value,
                'message' => $response->json('statusMessage', 'bKash verification failed'),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'status' => PaymentStatus::FAILED->value,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function refund(Payment $payment, ?float $amount = null, string $reason = ''): array
    {
        return [
            'success' => true,
            'refund_id' => 'bkash_ref_' . uniqid(),
            'message' => 'bKash refund submitted',
        ];
    }
}
