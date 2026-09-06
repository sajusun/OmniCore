<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Payment\Services\WalletService;
use App\Modules\Vendor\Enums\PayoutStatus;
use App\Modules\Vendor\Enums\VendorStatus;
use App\Modules\Vendor\Models\VendorStore;
use App\Modules\Vendor\Services\VendorService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class VendorTest extends TestCase
{
    use DatabaseTransactions;

    protected User $vendorUser;
    protected string $token;
    protected VendorService $vendorService;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        $this->vendorUser = User::factory()->create(['status' => 'active']);
        $this->token = auth('api')->login($this->vendorUser);

        $this->vendorService = app(VendorService::class);
    }

    public function test_can_list_active_vendor_stores(): void
    {
        VendorStore::create([
            'user_id'         => $this->vendorUser->id,
            'name'            => 'Electra Dynamics',
            'slug'            => 'electra-dynamics',
            'description'     => 'Electronics and robotics store',
            'commission_rate' => 10.00,
            'status'          => VendorStatus::ACTIVE->value,
            'is_featured'     => true,
        ]);

        $response = $this->getJson('/api/v1/vendors');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'code'   => 200,
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'slug', 'status', 'is_featured']
                ],
                'pagination'
            ]);
    }

    public function test_can_view_vendor_store_by_slug(): void
    {
        $store = VendorStore::create([
            'user_id'         => $this->vendorUser->id,
            'name'            => 'Gourmet Kitchen',
            'slug'            => 'gourmet-kitchen',
            'description'     => 'Artisanal kitchenware & spices',
            'commission_rate' => 12.00,
            'status'          => VendorStatus::ACTIVE->value,
        ]);

        $response = $this->getJson('/api/v1/vendors/store/' . $store->slug);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'code'   => 200,
                'data'   => [
                    'name' => 'Gourmet Kitchen',
                    'slug' => 'gourmet-kitchen',
                ]
            ]);
    }

    public function test_user_can_register_vendor_store(): void
    {
        $payload = [
            'name'        => 'NeoTech Store',
            'slug'        => 'neotech-store',
            'description' => 'Futuristic electronics and wearables',
            'phone'       => '+1234567890',
            'email'       => 'contact@neotech.example.com',
            'address'     => '45 Tech Plaza, Austin, TX',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/vendors/register', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'code'   => 201,
                'data'   => [
                    'name'   => 'NeoTech Store',
                    'slug'   => 'neotech-store',
                    'status' => 'active',
                ]
            ]);

        $this->assertDatabaseHas('vendor_stores', [
            'user_id' => $this->vendorUser->id,
            'name'    => 'NeoTech Store',
        ]);

        $this->assertDatabaseHas('vendor_members', [
            'user_id' => $this->vendorUser->id,
            'role'    => 'owner',
        ]);
    }

    public function test_vendor_can_view_my_store_dashboard(): void
    {
        $this->vendorService->registerStore($this->vendorUser, [
            'name' => 'Craftsman Studio',
            'slug' => 'craftsman-studio',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/vendors/my-store');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'code'   => 200,
                'data'   => [
                    'name' => 'Craftsman Studio',
                ]
            ]);
    }

    public function test_sales_recording_calculates_vendor_earnings_and_balance(): void
    {
        $store = $this->vendorService->registerStore($this->vendorUser, [
            'name'            => 'Velvet Apparel',
            'commission_rate' => 10.00, // 10% platform commission
        ]);

        // Record $500 sale
        $this->vendorService->recordSale($store, 500.00);

        $fresh = $store->fresh();
        $this->assertEquals(500.00, (float) $fresh->total_sales);
        $this->assertEquals(450.00, (float) $fresh->total_earnings); // $500 - 10% ($50) = $450
        $this->assertEquals(450.00, (float) $fresh->balance);
    }

    public function test_vendor_can_request_payout_to_wallet(): void
    {
        $store = $this->vendorService->registerStore($this->vendorUser, [
            'name'            => 'Nova Goods',
            'commission_rate' => 10.00,
        ]);

        // Record $1000 sale -> $900 balance
        $this->vendorService->recordSale($store, 1000.00);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/vendors/payouts/request', [
                'amount' => 400.00,
                'method' => 'wallet',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'code'   => 200,
                'data'   => [
                    'amount'        => 400.00,
                    'status'        => 'completed',
                    'payout_method' => 'wallet',
                ]
            ]);

        // Store balance should be reduced from 900 to 500
        $this->assertEquals(500.00, (float) $store->fresh()->balance);

        // User's in-app Wallet should now have $400 credited
        $walletService = app(WalletService::class);
        $wallet = $walletService->getOrCreateWallet($this->vendorUser);
        $this->assertEquals(400.00, (float) $wallet->balance);
    }
}
