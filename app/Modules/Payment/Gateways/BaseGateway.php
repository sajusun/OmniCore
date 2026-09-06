<?php

namespace App\Modules\Payment\Gateways;

use App\Modules\Payment\Contracts\PaymentGatewayInterface;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Models\PaymentGateway;

abstract class BaseGateway implements PaymentGatewayInterface
{
    protected ?PaymentGateway $gateway = null;

    public function setGateway(PaymentGateway $gateway): self
    {
        $this->gateway = $gateway;
        return $this;
    }

    protected function getCredential(string $key, mixed $default = null): mixed
    {
        return $this->gateway?->credentials[$key] ?? $default;
    }

    protected function isSandbox(): bool
    {
        return $this->gateway?->is_sandbox ?? true;
    }

    abstract public function charge(Payment $payment, array $options = []): array;

    abstract public function verify(Payment $payment, array $payload = []): array;

    abstract public function refund(Payment $payment, ?float $amount = null, string $reason = ''): array;
}
