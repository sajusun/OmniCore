<?php

namespace App\Modules\Payment\Services;

use App\Models\User;
use App\Modules\Payment\Enums\PaymentMethod;
use App\Modules\Payment\Enums\PaymentStatus;
use App\Modules\Payment\Gateways\GatewayManager;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Models\PaymentGateway;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class PaymentService
{
    public function __construct(
        protected GatewayManager $gatewayManager,
        protected WalletService $walletService
    ) {}

    public function generatePaymentId(): string
    {
        return 'PAY_' . date('YmdHis') . '_' . strtoupper(Str::random(6));
    }

    public function initiatePayment(
        User $user,
        Model $payable,
        float $amount,
        PaymentMethod $method,
        array $options = []
    ): Payment {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Payment amount must be greater than zero');
        }

        $gatewayModel = PaymentGateway::where('code', $method->value)->first();
        $fee = $gatewayModel ? $gatewayModel->calculateFee($amount) : 0.00;
        $currency = $options['currency'] ?? 'USD';

        $payment = Payment::create([
            'payment_id' => $this->generatePaymentId(),
            'user_id' => $user->id,
            'payable_type' => get_class($payable),
            'payable_id' => $payable->getKey(),
            'gateway' => $method->value,
            'amount' => $amount,
            'fee' => $fee,
            'currency' => strtoupper($currency),
            'status' => PaymentStatus::PENDING->value,
            'metadata' => $options['metadata'] ?? [],
        ]);

        $gatewayDriver = $this->gatewayManager->driver($method->value);
        $chargeResult = $gatewayDriver->charge($payment, $options);

        if (!empty($chargeResult['success'])) {
            $payment->checkout_url = $chargeResult['payment_url'] ?? null;
            if (!empty($chargeResult['transaction_id'])) {
                $payment->gateway_transaction_id = $chargeResult['transaction_id'];
            }
            $payment->save();

            if (($chargeResult['status'] ?? null) === PaymentStatus::COMPLETED->value) {
                $payment = $this->markAsCompleted($payment, $chargeResult['transaction_id'] ?? null, $chargeResult['raw'] ?? []);
            }
        } else {
            $payment->status = PaymentStatus::FAILED->value;
            $payment->failure_reason = $chargeResult['message'] ?? 'Payment initiation failed';
            $payment->save();
        }

        return $payment;
    }

    public function verifyPayment(Payment|string $payment, array $payload = []): Payment
    {
        $payment = is_string($payment)
            ? Payment::where('payment_id', $payment)->firstOrFail()
            : $payment;

        if ($payment->isCompleted()) {
            return $payment;
        }

        $gatewayDriver = $this->gatewayManager->driver($payment->gateway);
        $result = $gatewayDriver->verify($payment, $payload);

        if (!empty($result['success']) && ($result['status'] ?? '') === PaymentStatus::COMPLETED->value) {
            $this->markAsCompleted($payment, $result['transaction_id'] ?? null, $result['raw'] ?? []);
        } else {
            $this->markAsFailed($payment, $result['message'] ?? 'Verification failed');
        }

        return $payment->fresh();
    }

    public function markAsCompleted(Payment $payment, ?string $trxId = null, array $metadata = []): Payment
    {
        return DB::transaction(function () use ($payment, $trxId, $metadata) {
            $payment = Payment::where('id', $payment->id)->lockForUpdate()->first();

            if ($payment->status === PaymentStatus::COMPLETED->value) {
                return $payment;
            }

            $payment->status = PaymentStatus::COMPLETED->value;
            if ($trxId) {
                $payment->gateway_transaction_id = $trxId;
            }
            $payment->paid_at = now();
            if (!empty($metadata)) {
                $payment->gateway_response = $metadata;
            }
            $payment->save();

            // Notify or update the payable model if it has onPaymentSuccess method
            $payable = $payment->payable;
            if ($payable && method_exists($payable, 'onPaymentSuccess')) {
                $payable->onPaymentSuccess($payment);
            }

            return $payment;
        });
    }

    public function markAsFailed(Payment $payment, string $reason = 'Payment failed'): Payment
    {
        $payment->status = PaymentStatus::FAILED->value;
        $payment->failure_reason = $reason;
        $payment->save();

        $payable = $payment->payable;
        if ($payable && method_exists($payable, 'onPaymentFailed')) {
            $payable->onPaymentFailed($payment);
        }

        return $payment;
    }

    public function refundPayment(Payment $payment, ?float $amount = null, string $reason = ''): array
    {
        if (!$payment->isCompleted()) {
            throw new RuntimeException('Only completed payments can be refunded');
        }

        $gatewayDriver = $this->gatewayManager->driver($payment->gateway);
        $result = $gatewayDriver->refund($payment, $amount, $reason);

        if (!empty($result['success'])) {
            $payment->status = PaymentStatus::REFUNDED->value;
            $payment->metadata = array_merge($payment->metadata ?? [], [
                'refund_id' => $result['refund_id'] ?? null,
                'refund_reason' => $reason,
                'refunded_at' => now()->toIso8601String(),
            ]);
            $payment->save();
        }

        return $result;
    }
}
