<?php

namespace App\Modules\Payment\Gateways;

use App\Modules\Payment\Enums\PaymentStatus;
use App\Modules\Payment\Models\Payment;

class ManualBankGateway extends BaseGateway
{
    public function charge(Payment $payment, array $options = []): array
    {
        $instructions = $this->getCredential('instructions', 'Please transfer to our bank account and provide the receipt/transaction ID.');

        return [
            'success' => true,
            'status' => PaymentStatus::PENDING->value,
            'transaction_id' => $options['transaction_id'] ?? null,
            'message' => 'Manual payment details recorded. Awaiting admin verification.',
            'instructions' => $instructions,
        ];
    }

    public function verify(Payment $payment, array $payload = []): array
    {
        return [
            'success' => $payment->isCompleted(),
            'status' => $payment->status->value,
            'transaction_id' => $payment->gateway_transaction_id,
            'message' => $payment->isCompleted() ? 'Manual payment approved by admin' : 'Awaiting admin verification',
        ];
    }

    public function refund(Payment $payment, ?float $amount = null, string $reason = ''): array
    {
        return [
            'success' => true,
            'refund_id' => 'manual_ref_' . uniqid(),
            'message' => 'Manual refund recorded',
        ];
    }
}
