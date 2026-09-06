<?php

namespace App\Modules\Payment\Database\Seeders;

use App\Modules\Payment\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        $gateways = [
            [
                'code' => 'stripe',
                'name' => 'Stripe Card Payment',
                'description' => 'Pay securely via Credit/Debit Cards with Stripe.',
                'logo' => null,
                'is_active' => true,
                'is_test_mode' => true,
                'currencies' => ['USD', 'EUR', 'GBP', 'BDT'],
                'charge_fixed' => 0.30,
                'charge_percentage' => 2.90,
                'credentials' => [
                    'publishable_key' => '',
                    'secret_key' => '',
                    'webhook_secret' => '',
                ],
                'sort_order' => 1,
            ],
            [
                'code' => 'paypal',
                'name' => 'PayPal',
                'description' => 'Pay via your PayPal account or linked cards.',
                'logo' => null,
                'is_active' => true,
                'is_test_mode' => true,
                'currencies' => ['USD', 'EUR', 'GBP'],
                'charge_fixed' => 0.30,
                'charge_percentage' => 3.49,
                'credentials' => [
                    'client_id' => '',
                    'client_secret' => '',
                    'app_id' => '',
                ],
                'sort_order' => 2,
            ],
            [
                'code' => 'sslcommerz',
                'name' => 'SSLCommerz',
                'description' => 'Pay via Cards, Net Banking, and Mobile Wallets in Bangladesh.',
                'logo' => null,
                'is_active' => true,
                'is_test_mode' => true,
                'currencies' => ['BDT', 'USD'],
                'charge_fixed' => 0.00,
                'charge_percentage' => 2.50,
                'credentials' => [
                    'store_id' => '',
                    'store_password' => '',
                ],
                'sort_order' => 3,
            ],
            [
                'code' => 'bkash',
                'name' => 'bKash Direct',
                'description' => 'Instant tokenized payment via bKash mobile financial service.',
                'logo' => null,
                'is_active' => true,
                'is_test_mode' => true,
                'currencies' => ['BDT'],
                'charge_fixed' => 0.00,
                'charge_percentage' => 1.50,
                'credentials' => [
                    'app_key' => '',
                    'app_secret' => '',
                    'username' => '',
                    'password' => '',
                ],
                'sort_order' => 4,
            ],
            [
                'code' => 'wallet',
                'name' => 'In-App Wallet',
                'description' => 'Instant deduction from your personal app wallet balance.',
                'logo' => null,
                'is_active' => true,
                'is_test_mode' => false,
                'currencies' => ['USD', 'BDT', 'EUR', 'GBP'],
                'charge_fixed' => 0.00,
                'charge_percentage' => 0.00,
                'credentials' => [],
                'sort_order' => 5,
            ],
            [
                'code' => 'manual_bank',
                'name' => 'Direct Bank Transfer',
                'description' => 'Direct transfer to our bank account with receipt verification.',
                'logo' => null,
                'is_active' => true,
                'is_test_mode' => false,
                'currencies' => ['USD', 'BDT', 'EUR', 'GBP'],
                'charge_fixed' => 0.00,
                'charge_percentage' => 0.00,
                'credentials' => [
                    'instructions' => 'Bank: City Bank Ltd | A/C: 1234567890 | Branch: Gulshan | Title: One Dashboard Ltd',
                ],
                'sort_order' => 6,
            ],
            [
                'code' => 'cod',
                'name' => 'Cash on Delivery (COD)',
                'description' => 'Pay cash upon delivery of your physical products.',
                'logo' => null,
                'is_active' => true,
                'is_test_mode' => false,
                'currencies' => ['USD', 'BDT'],
                'charge_fixed' => 0.00,
                'charge_percentage' => 0.00,
                'credentials' => [],
                'sort_order' => 7,
            ],
        ];

        foreach ($gateways as $gateway) {
            PaymentGateway::updateOrCreate(
                ['code' => $gateway['code']],
                $gateway
            );
        }
    }
}
