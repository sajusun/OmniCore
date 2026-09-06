<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Payment\Services\WalletService;
use App\Modules\Subscription\Database\Seeders\SubscriptionPlanSeeder;
use App\Modules\Subscription\Models\Plan;
use App\Modules\Subscription\Models\Subscription;
use App\Modules\Subscription\Services\SubscriptionService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected SubscriptionService $subscriptionService;
    protected WalletService $walletService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'status' => 'active',
        ]);

        $this->subscriptionService = app(SubscriptionService::class);
        $this->walletService = app(WalletService::class);

        $this->seed(SubscriptionPlanSeeder::class);
    }

    public function test_can_list_public_subscription_plans(): void
    {
        $response = $this->getJson('/api/plans');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => ['id', 'uuid', 'name', 'slug', 'price', 'billing_interval', 'features'],
                ],
            ]);
    }

    public function test_can_view_single_plan_details(): void
    {
        $response = $this->getJson('/api/plans/pro-monthly');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'slug' => 'pro-monthly',
                    'price' => 19.99,
                ],
            ]);
    }

    public function test_user_can_subscribe_to_free_plan(): void
    {
        $freePlan = Plan::where('slug', 'free-starter')->firstOrFail();

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/subscriptions/subscribe', [
                'plan_id' => $freePlan->id,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'status' => 'active',
                    'plan' => [
                        'slug' => 'free-starter',
                    ],
                ],
            ]);

        $this->assertTrue($this->user->fresh()->subscribed('free-starter'));
        $this->assertTrue($this->user->fresh()->canAccessFeature('chat_messages'));
    }

    public function test_user_can_subscribe_to_paid_plan_using_wallet(): void
    {
        // Credit user wallet first
        $this->walletService->deposit($this->user, 100.00);

        $proPlan = Plan::where('slug', 'pro-monthly')->firstOrFail();

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/subscriptions/subscribe', [
                'plan_id' => $proPlan->id,
                'payment_method' => 'wallet',
            ]);

        $response->assertStatus(200);

        // Pro monthly has trial days, or if activated directly
        $this->assertTrue($this->user->fresh()->subscribed('pro-monthly'));
        $this->assertTrue($this->user->fresh()->canAccessFeature('hd_video'));
        $this->assertTrue($this->user->fresh()->canAccessFeature('ai_queries'));
    }

    public function test_feature_quota_consumption_and_limits(): void
    {
        // Subscribe to Free plan with 10 post uploads limit
        $freePlan = Plan::where('slug', 'free-starter')->firstOrFail();
        $this->subscriptionService->subscribe($this->user, $freePlan);

        // Consuming 8 uploads should succeed
        $consumed = $this->user->consumeFeature('post_uploads', 8);
        $this->assertTrue($consumed);
        $this->assertEquals(2, $this->user->getRemainingFeatureUsage('post_uploads'));

        // Consuming 5 more uploads should fail (exceeds limit)
        $consumedMore = $this->user->consumeFeature('post_uploads', 5);
        $this->assertFalse($consumedMore);
        $this->assertEquals(2, $this->user->getRemainingFeatureUsage('post_uploads'));
    }

    public function test_user_can_cancel_and_resume_subscription(): void
    {
        $freePlan = Plan::where('slug', 'free-starter')->firstOrFail();
        $this->subscriptionService->subscribe($this->user, $freePlan);

        // 1. Cancel
        $cancelResponse = $this->actingAs($this->user, 'api')
            ->postJson('/api/subscriptions/cancel');

        $cancelResponse->assertStatus(200);

        $sub = $this->user->currentSubscription();
        $this->assertTrue($sub->onGracePeriod());

        // 2. Resume
        $resumeResponse = $this->actingAs($this->user, 'api')
            ->postJson('/api/subscriptions/resume');

        $resumeResponse->assertStatus(200);
        $this->assertFalse($this->user->currentSubscription()->canceled());
    }
}
