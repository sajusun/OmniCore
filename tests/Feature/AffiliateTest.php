<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Affiliate\Enums\AffiliateStatus;
use App\Modules\Affiliate\Enums\CommissionType;
use App\Modules\Affiliate\Models\AffiliateAccount;
use App\Modules\Affiliate\Services\AffiliateService;
use App\Modules\Payment\Services\WalletService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AffiliateTest extends TestCase
{
    use DatabaseTransactions;

    protected User $affiliateUser;
    protected User $referredUser;
    protected string $token;
    protected AffiliateService $affiliateService;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        $this->affiliateUser = User::factory()->create(['status' => 'active']);
        $this->referredUser = User::factory()->create(['status' => 'active']);
        $this->token = auth('api')->login($this->affiliateUser);

        $this->affiliateService = app(AffiliateService::class);
    }

    public function test_user_can_get_affiliate_account(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/affiliate/account');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'code'   => 200,
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'referral_code',
                    'referral_url',
                    'commission_rate',
                    'total_earnings',
                    'current_balance',
                    'lifetime_referrals',
                ]
            ]);
    }

    public function test_can_track_inbound_referral_click(): void
    {
        $account = $this->affiliateService->getOrCreateAccount($this->affiliateUser);

        $response = $this->postJson('/api/v1/affiliate/track', [
            'ref'          => $account->referral_code,
            'landing_page' => 'https://example.com/summer-sale',
            'campaign'     => 'twitter_promo',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'code'   => 200,
                'data'   => [
                    'tracked' => true,
                    'code'    => $account->referral_code,
                ]
            ]);

        $this->assertEquals(1, $account->fresh()->lifetime_referrals);
    }

    public function test_conversion_awards_commission_to_affiliate(): void
    {
        $account = $this->affiliateService->getOrCreateAccount($this->affiliateUser);
        $account->update(['commission_rate' => 10.00]); // 10%

        // Link referred user
        $referral = $this->affiliateService->linkUserToAffiliate($this->referredUser, $account->referral_code);
        $this->assertNotNull($referral);

        // Record $200 order conversion
        $commission = $this->affiliateService->recordConversion($this->referredUser, 200.00);

        $this->assertNotNull($commission);
        $this->assertEquals(20.00, (float) $commission->commission_amount);
        $this->assertEquals(20.00, (float) $account->fresh()->current_balance);
        $this->assertEquals(1, $account->fresh()->lifetime_conversions);
    }

    public function test_user_can_view_referral_and_commission_logs(): void
    {
        $account = $this->affiliateService->getOrCreateAccount($this->affiliateUser);
        $this->affiliateService->linkUserToAffiliate($this->referredUser, $account->referral_code);
        $this->affiliateService->recordConversion($this->referredUser, 100.00);

        // Check referrals endpoint
        $refResponse = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/affiliate/referrals');

        $refResponse->assertStatus(200)
            ->assertJson([
                'status' => true,
                'code'   => 200,
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'status', 'created_at']
                ],
                'pagination'
            ]);

        // Check commissions endpoint
        $commResponse = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/affiliate/commissions');

        $commResponse->assertStatus(200)
            ->assertJson([
                'status' => true,
                'code'   => 200,
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'order_amount', 'commission_amount', 'status']
                ],
                'pagination'
            ]);
    }

    public function test_user_can_update_custom_vanity_slug(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->patchJson('/api/v1/affiliate/slug', [
                'custom_slug' => 'tech-guru-special',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.custom_slug', 'tech-guru-special');

        $account = $this->affiliateService->getOrCreateAccount($this->affiliateUser);
        $this->assertEquals('tech-guru-special', $account->fresh()->custom_slug);
    }

    public function test_user_can_payout_earnings_to_wallet(): void
    {
        $account = $this->affiliateService->getOrCreateAccount($this->affiliateUser);
        $account->update(['current_balance' => 75.00]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/affiliate/payout', [
                'amount' => 50.00,
            ]);

        $response->assertStatus(200);
        $this->assertEquals(25.00, (float) $response->json('data.current_balance'));
        $this->assertEquals(50.00, (float) $response->json('data.paid_earnings'));

        // Verify wallet balance
        $walletService = app(WalletService::class);
        $wallet = $walletService->getOrCreateWallet($this->affiliateUser);
        $this->assertEquals(50.00, (float) $wallet->balance);
    }
}
