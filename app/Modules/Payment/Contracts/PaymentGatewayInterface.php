<?php

namespace App\Modules\Payment\Contracts;

use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Models\PaymentGateway;

interface PaymentGatewayInterface
{
    /**
     * Set gateway model/credentials
     */
    public function setGateway(PaymentGateway $gateway): self;

    /**
     * Charge payment or create checkout session
     *
     * @return array{success: bool, payment_url?: string|null, transaction_id?: string|null, message?: string|null, raw?: mixed}
     */
    public function charge(Payment $payment, array $options = []): array;

    /**
     * Verify payment status with gateway
     *
     * @return array{success: bool, status: string, transaction_id?: string|null, message?: string|null, raw?: mixed}
     */
    public function verify(Payment $payment, array $payload = []): array;

    /**
     * Refund payment
     *
     * @return array{success: bool, refund_id?: string|null, message?: string|null, raw?: mixed}
     */
    public function refund(Payment $payment, ?float $amount = null, string $reason = ''): array;
}
