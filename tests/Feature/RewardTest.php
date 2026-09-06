<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Reward\Database\Seeders\RewardSeeder;
use App\Modules\Reward\Models\Badge;
use App\Modules\Reward\Models\RewardTier;
use App\Modules\Reward\Services\RewardService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RewardTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected RewardService $rewardService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'status' => 'active',
        ]);

        $this->rewardService = app(RewardService::class);
        $this->seed(RewardSeeder::class);
    }

    public function test_can_list_reward_tiers(): void
    {
        $response = $this->getJson('/api/rewards/tiers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => ['id', 'name', 'slug', 'min_points', 'point_multiplier', 'discount_percent', 'perks'],
                ],
            ]);
    }

    public function test_user_can_get_reward_overview(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/rewards');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'points_balance' => 0,
                    'streak_days' => 0,
                    'can_checkin_today' => true,
                ],
            ]);
    }

    public function test_user_can_claim_daily_checkin_and_cannot_claim_twice(): void
    {
        // 1. First claim
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/rewards/checkin');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'streak_days' => 1,
                    'points_earned' => 10,
                ],
            ]);

        $this->assertEquals(10, $this->user->fresh()->points_balance);

        // 2. Second claim on same day must fail
        $secondResponse = $this->actingAs($this->user, 'api')
            ->postJson('/api/rewards/checkin');

        $secondResponse->assertStatus(400);
    }

    public function test_points_earning_and_automatic_tier_promotion(): void
    {
        // Add 600 points (Bronze -> Silver threshold is 500)
        $this->rewardService->addPoints($this->user, 600);

        $this->assertEquals(600, $this->user->fresh()->points_balance);
        $this->assertEquals('silver-tier', $this->user->fresh()->tier->slug);

        // Point multiplier for Silver is 1.25x
        // Adding 100 points will give 125 points
        $this->rewardService->addPoints($this->user, 100);
        $this->assertEquals(725, $this->user->fresh()->points_balance);
    }

    public function test_user_can_redeem_points_to_wallet_cash(): void
    {
        // Add 500 points
        $this->rewardService->addPoints($this->user, 500);

        // Redeem 300 points ($3.00 wallet credit)
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/rewards/redeem', [
                'points' => 300,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'points_deducted' => 300,
                    'cash_credited' => 3.00,
                    'new_points_balance' => 200,
                    'new_wallet_balance' => 3.00,
                ],
            ]);

        // Check wallet balance
        $this->assertEquals(3.00, (float) $this->user->fresh()->wallet_balance);
        $this->assertEquals(200, $this->user->fresh()->points_balance);
    }

    public function test_can_view_badges_and_leaderboard(): void
    {
        // Add points
        $this->rewardService->addPoints($this->user, 250);

        // Badges endpoint
        $badgesResponse = $this->actingAs($this->user, 'api')
            ->getJson('/api/rewards/badges');
        $badgesResponse->assertStatus(200);

        // Leaderboard endpoint
        $leaderboardResponse = $this->getJson('/api/rewards/leaderboard');
        $leaderboardResponse->assertStatus(200);
    }
}
