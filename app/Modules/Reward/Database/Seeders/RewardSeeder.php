<?php

namespace App\Modules\Reward\Database\Seeders;

use App\Modules\Reward\Models\Badge;
use App\Modules\Reward\Models\RewardTier;
use Illuminate\Database\Seeder;

class RewardSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            [
                'name' => 'Bronze Tier',
                'slug' => 'bronze-tier',
                'description' => 'Entry level loyalty status for all new members.',
                'min_points' => 0,
                'point_multiplier' => 1.00,
                'discount_percent' => 0.00,
                'perks' => ['Standard 1x point earnings on all purchases', 'Access to daily check-in rewards'],
                'color' => '#cd7f32',
                'sort_order' => 1,
            ],
            [
                'name' => 'Silver Tier',
                'slug' => 'silver-tier',
                'description' => 'Active members enjoying elevated perks and discounts.',
                'min_points' => 500,
                'point_multiplier' => 1.25,
                'discount_percent' => 5.00,
                'perks' => ['1.25x point multiplier on purchases', '5% automatic store discount', 'Priority customer support'],
                'color' => '#9ca3af',
                'sort_order' => 2,
            ],
            [
                'name' => 'Gold Tier',
                'slug' => 'gold-tier',
                'description' => 'Premier loyalty members with exclusive seasonal discounts.',
                'min_points' => 2000,
                'point_multiplier' => 1.50,
                'discount_percent' => 10.00,
                'perks' => ['1.50x point multiplier', '10% automatic store discount', 'Free shipping on physical orders', 'Exclusive member deals'],
                'color' => '#eab308',
                'sort_order' => 3,
            ],
            [
                'name' => 'Platinum VIP',
                'slug' => 'platinum-vip',
                'description' => 'Top-tier VIP membership with maximum privileges.',
                'min_points' => 5000,
                'point_multiplier' => 2.00,
                'discount_percent' => 15.00,
                'perks' => ['2.0x double point multiplier', '15% automatic store discount', 'Free express shipping', 'Dedicated VIP account concierge'],
                'color' => '#06b6d4',
                'sort_order' => 4,
            ],
        ];

        foreach ($tiers as $t) {
            RewardTier::updateOrCreate(['slug' => $t['slug']], $t);
        }

        $badges = [
            [
                'name' => 'First Order Champion',
                'slug' => 'first-order-champion',
                'description' => 'Placed your first successful order on the platform.',
                'badge_type' => 'achievement',
                'points_reward' => 50,
                'criteria_type' => 'orders_count',
                'criteria_threshold' => 1,
                'is_active' => true,
            ],
            [
                'name' => '7-Day Streak Master',
                'slug' => '7-day-streak-master',
                'description' => 'Completed 7 consecutive daily check-ins.',
                'badge_type' => 'streak',
                'points_reward' => 100,
                'criteria_type' => 'checkin_streak',
                'criteria_threshold' => 7,
                'is_active' => true,
            ],
            [
                'name' => '30-Day Dedication Legend',
                'slug' => '30-day-dedication-legend',
                'description' => 'Maintained an unbroken 30-day daily check-in streak.',
                'badge_type' => 'streak',
                'points_reward' => 500,
                'criteria_type' => 'checkin_streak',
                'criteria_threshold' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Community Connector',
                'slug' => 'community-connector',
                'description' => 'Successfully invited 5 verified friends.',
                'badge_type' => 'achievement',
                'points_reward' => 150,
                'criteria_type' => 'referral_count',
                'criteria_threshold' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($badges as $b) {
            Badge::updateOrCreate(['slug' => $b['slug']], $b);
        }
    }
}
