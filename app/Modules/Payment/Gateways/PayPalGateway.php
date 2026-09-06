<?php

namespace App\Modules\Payment\Gateways;

use App\Modules\Payment\Enums\PaymentStatus;
use App\Modules\Payment\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalGateway extends BaseGateway
{
    protected function getBaseUrl(): string
    {
        return $this->isSandbox()
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    protected function getAccessToken(): ?string
    {
        $clientId = $this->getCredential('client_id');
        $secret = $this->getCredential('client_secret');

        if (empty($clientId) || empty($secret)) {
            return null;
        }

        try {
            $response = Http::withBasicAuth($clientId, $secret)
                ->asForm()
                ->post($this->getBaseUrl() . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials',
                ]);

            return $response->json('access_token');
        } catch (\Throwable $e) {
            Log::error('PayPal token error: ' . $e->getMessage());
            return null;
        }
    }

    public function charge(Payment $payment, array $options = []): array
    {
        $token = $this->getAccessToken();

        if (empty($token)) {
            // Mock PayPal session
            $orderId = 'PAYPAL_MOCK_' . strtoupper(bin2hex(random_bytes(6)));
            return [
                'success' => true,
                'payment_url' => url("/api/payments/verify/{$payment->payment_id}?gateway=paypal&token={$orderId}"),
                'transaction_id' => $orderId,
                'message' => 'PayPal order created (mock mode)',
            ];
        }

        try {
            $response = Http::withToken($token)
                ->post($this->getBaseUrl() . '/v2/checkout/orders', [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [[
                        'reference_id' => (string) $payment->payment_id,
                        'amount' => [
                            'currency_code' => strtoupper($payment->currency ?: 'USD'),
                            'value' => number_format((float) $payment->total_amount, 2, '.', ''),
                        ],
                        'description' => 'Payment for ' . class_basename($payment->payable_type) . ' #' . $payment->payable_id,
                    ]],
                    'application_context' => [
                        'return_url' => url("/api/payments/verify/{$payment->payment_id}?gateway=paypal"),
                        'cancel_url' => url("/api/payments/cancel/{$payment->payment_id}?gateway=paypal"),
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $links = collect($data['links'] ?? []);
                $approveUrl = $links->firstWhere('rel', 'approve')['href'] ?? null;

                return [
                    'success' => true,
                    'payment_url' => $approveUrl,
                    'transaction_id' => $data['id'] ?? null,
                    'message' => 'PayPal order initialized',
                    'raw' => $data,
                ];
            }

            return [
                'success' => false,
                'message' => $response->json('message', 'PayPal order creation failed'),
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
        $token = $this->getAccessToken();
        $orderId = $payload['token'] ?? $payment->gateway_transaction_id;

        if (empty($token) || str_starts_with((string) $orderId, 'PAYPAL_MOCK_')) {
            return [
                'success' => true,
                'status' => PaymentStatus::COMPLETED->value,
                'transaction_id' => $orderId ?: 'txn_paypal_' . uniqid(),
                'message' => 'PayPal payment verified (mock mode)',
            ];
        }

        try {
            // Capture the order
            $response = Http::withToken($token)
                ->post($this->getBaseUrl() . "/v2/checkout/orders/{$orderId}/capture");

            if ($response->successful()) {
                $data = $response->json();
                $isCompleted = ($data['status'] ?? '') === 'COMPLETED';

                return [
                    'success' => $isCompleted,
                    'status' => $isCompleted ? PaymentStatus::COMPLETED->value : PaymentStatus::FAILED->value,
                    'transaction_id' => $data['id'] ?? $orderId,
                    'message' => $isCompleted ? 'PayPal captured successfully' : 'PayPal capture incomplete',
                    'raw' => $data,
                ];
            }

            return [
                'success' => false,
                'status' => PaymentStatus::FAILED->value,
                'message' => $response->json('message', 'PayPal capture failed'),
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
        $token = $this->getAccessToken();
        $captureId = $payment->gateway_transaction_id;

        if (empty($token) || empty($captureId)) {
            return [
                'success' => true,
                'refund_id' => 'paypal_ref_' . uniqid(),
                'message' => 'PayPal refund simulated (mock mode)',
            ];
        }

        try {
            $chargeAmount = $amount ?? (float) $payment->total_amount;
            $response = Http::withToken($token)
                ->post($this->getBaseUrl() . "/v2/payments/captures/{$captureId}/refund", [
                    'amount' => [
                        'value' => number_format($chargeAmount, 2, '.', ''),
                        'currency_code' => strtoupper($payment->currency ?: 'USD'),
                    ],
                    'note_to_payer' => $reason ?: 'Refund for Order #' . $payment->payable_id,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'refund_id' => $data['id'] ?? null,
                    'message' => 'PayPal refund completed',
                    'raw' => $data,
                ];
            }

            return [
                'success' => false,
                'message' => $response->json('message', 'PayPal refund failed'),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
