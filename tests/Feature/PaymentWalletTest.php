<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Order\Models\Order;
use App\Modules\Payment\Enums\WithdrawalStatus;
use App\Modules\Payment\Services\PaymentService;
use App\Modules\Payment\Services\WalletService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PaymentWalletTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected User $user2;
    protected WalletService $walletService;
    protected PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'status' => 'active',
        ]);

        $this->user2 = User::factory()->create([
            'status' => 'active',
        ]);

        $this->walletService = app(WalletService::class);
        $this->paymentService = app(PaymentService::class);

        $this->seed(\App\Modules\Payment\Database\Seeders\PaymentGatewaySeeder::class);
    }

    public function test_can_list_active_payment_gateways(): void
    {
        $response = $this->getJson('/api/payments/gateways');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => ['id', 'code', 'name', 'is_active', 'fee_fixed', 'fee_percent'],
                ],
            ]);
    }

    public function test_user_can_get_wallet_balance(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/wallet');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'user_id' => $this->user->id,
                    'balance' => 0.00,
                    'is_active' => true,
                    'is_frozen' => false,
                ],
            ]);
    }

    public function test_wallet_service_can_deposit_funds(): void
    {
        $trx = $this->walletService->deposit($this->user, 150.00, null, 'Test deposit');

        $this->assertEquals(150.00, (float) $trx->balance_after);
        $this->assertEquals(150.00, (float) $this->user->fresh()->wallet_balance);
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $this->user->id,
            'amount' => 150.00,
            'type' => 'deposit',
        ]);
    }

    public function test_p2p_wallet_transfer_between_users(): void
    {
        // Credit sender first
        $this->walletService->deposit($this->user, 200.00);

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/wallet/transfer', [
                'recipient_id' => $this->user2->id,
                'amount' => 75.50,
                'note' => 'Payment for service',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'current_balance' => 124.50,
                ],
            ]);

        $this->assertEquals(124.50, (float) $this->user->fresh()->wallet_balance);
        $this->assertEquals(75.50, (float) $this->user2->fresh()->wallet_balance);
    }

    public function test_cannot_transfer_more_than_wallet_balance(): void
    {
        $this->walletService->deposit($this->user, 50.00);

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/wallet/transfer', [
                'recipient_id' => $this->user2->id,
                'amount' => 100.00,
            ]);

        $response->assertStatus(400);
    }

    public function test_user_can_request_and_cancel_withdrawal(): void
    {
        // Credit user wallet
        $this->walletService->deposit($this->user, 500.00);

        // 1. Submit withdrawal request
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/withdrawals', [
                'amount' => 200.00,
                'method' => 'bank_transfer',
                'account_details' => [
                    'account_number' => '1234567890',
                    'bank_name' => 'Standard Chartered',
                ],
            ]);

        $response->assertStatus(201);
        $withdrawalId = $response->json('data.id');

        // Balance must be deducted immediately upon request
        $this->assertEquals(300.00, (float) $this->user->fresh()->wallet_balance);

        // 2. User cancels pending withdrawal
        $cancelResponse = $this->actingAs($this->user, 'api')
            ->postJson("/api/withdrawals/{$withdrawalId}/cancel");

        $cancelResponse->assertStatus(200);

        // Balance must be refunded back
        $this->assertEquals(500.00, (float) $this->user->fresh()->wallet_balance);
        $this->assertDatabaseHas('withdrawal_requests', [
            'id' => $withdrawalId,
            'status' => WithdrawalStatus::CANCELLED->value,
        ]);
    }

    public function test_user_can_pay_for_order_using_wallet(): void
    {
        // Credit user wallet
        $this->walletService->deposit($this->user, 300.00);

        // Create order
        $order = Order::create([
            'order_number' => 'ORD-' . uniqid(),
            'user_id' => $this->user->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'wallet',
            'subtotal' => 120.00,
            'total_amount' => 120.00,
            'shipping_address' => ['address' => '123 Test St', 'city' => 'Dhaka'],
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/payments/initiate', [
                'payable_type' => 'Order',
                'payable_id' => $order->id,
                'amount' => 120.00,
                'method' => 'wallet',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'status' => 'completed',
                ],
            ]);

        // Verify order is paid
        $this->assertEquals('paid', $order->fresh()->payment_status);
        // Verify wallet is deducted
        $this->assertEquals(180.00, (float) $this->user->fresh()->wallet_balance);
    }

    public function test_user_can_initiate_and_verify_stripe_mock_payment(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-' . uniqid(),
            'user_id' => $this->user->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'stripe',
            'subtotal' => 50.00,
            'total_amount' => 50.00,
            'shipping_address' => ['address' => '123 Test St', 'city' => 'Dhaka'],
        ]);

        $initiateResponse = $this->actingAs($this->user, 'api')
            ->postJson('/api/payments/initiate', [
                'payable_type' => Order::class,
                'payable_id' => $order->id,
                'amount' => 50.00,
                'method' => 'stripe',
            ]);

        $initiateResponse->assertStatus(200);
        $paymentId = $initiateResponse->json('data.payment.payment_id');

        // Verify endpoint
        $verifyResponse = $this->getJson("/api/payments/verify/{$paymentId}?session_id=cs_test_mock123");

        $verifyResponse->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'status' => 'completed',
                ],
            ]);

        $this->assertEquals('paid', $order->fresh()->payment_status);
    }
}
