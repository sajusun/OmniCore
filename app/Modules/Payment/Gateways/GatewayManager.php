<?php

namespace App\Modules\Payment\Gateways;

use App\Modules\Payment\Contracts\PaymentGatewayInterface;
use App\Modules\Payment\Models\PaymentGateway;
use InvalidArgumentException;

class GatewayManager
{
    /**
     * Driver mapping
     */
    protected array $drivers = [
        'stripe' => StripeGateway::class,
        'paypal' => PayPalGateway::class,
        'sslcommerz' => SslCommerzGateway::class,
        'bkash' => BkashGateway::class,
        'wallet' => WalletGateway::class,
        'manual_bank' => ManualBankGateway::class,
        'cod' => ManualBankGateway::class,
    ];

    public function driver(string $code): PaymentGatewayInterface
    {
        $code = strtolower($code);

        if (!isset($this->drivers[$code])) {
            throw new InvalidArgumentException("Unsupported payment gateway: {$code}");
        }

        $class = $this->drivers[$code];
        /** @var PaymentGatewayInterface $instance */
        $instance = app($class);

        $gatewayModel = PaymentGateway::where('code', $code)->first();
        if ($gatewayModel) {
            $instance->setGateway($gatewayModel);
        }

        return $instance;
    }

    public function registerDriver(string $code, string $driverClass): void
    {
        $this->drivers[strtolower($code)] = $driverClass;
    }
}
