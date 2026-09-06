<?php

namespace App\Modules\Payment\Gateways;

use App\Modules\Payment\Enums\PaymentStatus;
use App\Modules\Payment\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripeGateway extends BaseGateway
{
    public function charge(Payment $payment, array $options = []): array
    {
        $secretKey = $this->getCredential('secret_key') ?? config('services.stripe.secret');

        if (empty($secretKey)) {
            // Mock checkout session for local / testing mode
            $mockSessionId = 'cs_test_' . bin2hex(random_bytes(12));
            return [
                'success' => true,
                'payment_url' => url("/api/payments/verify/{$payment->payment_id}?gateway=stripe&session_id={$mockSessionId}"),
                'transaction_id' => $mockSessionId,
                'message' => 'Stripe session created (mock mode)',
            ];
        }

        try {
            $response = Http::withBasicAuth($secretKey, '')
                ->asForm()
                ->post('https://api.stripe.com/v1/checkout/sessions', [
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => strtolower($payment->currency ?: 'usd'),
                            'unit_amount' => (int) round($payment->total_amount * 100),
                            'product_data' => [
                                'name' => 'Payment #' . $payment->payment_id,
                                'description' => 'Payment for ' . class_basename($payment->payable_type) . ' #' . $payment->payable_id,
                            ],
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => url("/api/payments/verify/{$payment->payment_id}?gateway=stripe&session_id={CHECKOUT_SESSION_ID}"),
                    'cancel_url' => url("/api/payments/cancel/{$payment->payment_id}?gateway=stripe"),
                    'client_reference_id' => (string) $payment->payment_id,
                    'customer_email' => $payment->user?->email,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'payment_url' => $data['url'] ?? null,
                    'transaction_id' => $data['id'] ?? null,
                    'message' => 'Stripe checkout session initialized',
                    'raw' => $data,
                ];
            }

            return [
                'success' => false,
                'message' => $response->json('error.message', 'Stripe checkout failed'),
                'raw' => $response->json(),
            ];
        } catch (\Throwable $e) {
            Log::error('Stripe charge error: ' . $e->getMessage(), ['payment_id' => $payment->payment_id]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function verify(Payment $payment, array $payload = []): array
    {
        $secretKey = $this->getCredential('secret_key') ?? config('services.stripe.secret');
        $sessionId = $payload['session_id'] ?? $payment->gateway_transaction_id;

        if (empty($secretKey) || str_starts_with((string) $sessionId, 'cs_test_')) {
            return [
                'success' => true,
                'status' => PaymentStatus::COMPLETED->value,
                'transaction_id' => $sessionId ?: 'txn_stripe_' . uniqid(),
                'message' => 'Stripe payment verified successfully (mock mode)',
            ];
        }

        try {
            $response = Http::withBasicAuth($secretKey, '')
                ->get("https://api.stripe.com/v1/checkout/sessions/{$sessionId}");

            if ($response->successful()) {
                $data = $response->json();
                $isPaid = ($data['payment_status'] ?? '') === 'paid';

                return [
                    'success' => $isPaid,
                    'status' => $isPaid ? PaymentStatus::COMPLETED->value : PaymentStatus::FAILED->value,
                    'transaction_id' => $data['payment_intent'] ?? $sessionId,
                    'message' => $isPaid ? 'Stripe payment confirmed' : 'Payment not completed',
                    'raw' => $data,
                ];
            }

            return [
                'success' => false,
                'status' => PaymentStatus::FAILED->value,
                'message' => $response->json('error.message', 'Stripe verification failed'),
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
        $secretKey = $this->getCredential('secret_key') ?? config('services.stripe.secret');
        $chargeAmount = $amount ?? (float) $payment->total_amount;

        if (empty($secretKey) || empty($payment->gateway_transaction_id)) {
            return [
                'success' => true,
                'refund_id' => 're_test_' . bin2hex(random_bytes(10)),
                'message' => 'Stripe refunded (mock mode)',
            ];
        }

        try {
            $params = [
                'payment_intent' => $payment->gateway_transaction_id,
                'amount' => (int) round($chargeAmount * 100),
            ];
            if ($reason) {
                $params['reason'] = 'requested_by_customer';
            }

            $response = Http::withBasicAuth($secretKey, '')
                ->asForm()
                ->post('https://api.stripe.com/v1/refunds', $params);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'refund_id' => $data['id'] ?? null,
                    'message' => 'Refund processed via Stripe',
                    'raw' => $data,
                ];
            }

            return [
                'success' => false,
                'message' => $response->json('error.message', 'Stripe refund failed'),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
