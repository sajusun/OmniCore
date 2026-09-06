<?php

namespace App\Modules\Subscription\Database\Seeders;

use App\Modules\Subscription\Models\Plan;
use App\Modules\Subscription\Models\PlanFeature;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free Starter',
                'slug' => 'free-starter',
                'description' => 'Essential access for individual users getting started.',
                'price' => 0.00,
                'signup_fee' => 0.00,
                'currency' => 'USD',
                'billing_interval' => 'month',
                'interval_count' => 1,
                'trial_days' => 0,
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 1,
                'features' => [
                    ['name' => 'Direct Messaging', 'code' => 'chat_messages', 'value' => '50', 'is_limited' => true],
                    ['name' => 'Post Uploads', 'code' => 'post_uploads', 'value' => '10', 'is_limited' => true],
                    ['name' => 'Standard Support', 'code' => 'standard_support', 'value' => 'true', 'is_limited' => false],
                ],
            ],
            [
                'name' => 'Pro Monthly',
                'slug' => 'pro-monthly',
                'description' => 'Advanced tools, unlimited messaging, and AI capabilities.',
                'price' => 19.99,
                'signup_fee' => 0.00,
                'currency' => 'USD',
                'billing_interval' => 'month',
                'interval_count' => 1,
                'trial_days' => 7,
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 2,
                'features' => [
                    ['name' => 'Unlimited Direct & Group Chat', 'code' => 'chat_messages', 'value' => 'unlimited', 'is_limited' => false],
                    ['name' => 'Post Uploads', 'code' => 'post_uploads', 'value' => '100', 'is_limited' => true],
                    ['name' => 'AI Queries Quota', 'code' => 'ai_queries', 'value' => '200', 'is_limited' => true],
                    ['name' => 'HD Video Calls', 'code' => 'hd_video', 'value' => 'true', 'is_limited' => false],
                    ['name' => 'Verified Badge', 'code' => 'verified_badge', 'value' => 'true', 'is_limited' => false],
                ],
            ],
            [
                'name' => 'Pro Yearly',
                'slug' => 'pro-yearly',
                'description' => 'Save 20% with annual billing. Full Pro access.',
                'price' => 189.99,
                'signup_fee' => 0.00,
                'currency' => 'USD',
                'billing_interval' => 'year',
                'interval_count' => 1,
                'trial_days' => 14,
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 3,
                'features' => [
                    ['name' => 'Unlimited Direct & Group Chat', 'code' => 'chat_messages', 'value' => 'unlimited', 'is_limited' => false],
                    ['name' => 'Unlimited Post Uploads', 'code' => 'post_uploads', 'value' => 'unlimited', 'is_limited' => false],
                    ['name' => 'AI Queries Quota', 'code' => 'ai_queries', 'value' => '2500', 'is_limited' => true],
                    ['name' => 'HD Video Calls', 'code' => 'hd_video', 'value' => 'true', 'is_limited' => false],
                    ['name' => 'Verified Badge', 'code' => 'verified_badge', 'value' => 'true', 'is_limited' => false],
                    ['name' => 'Priority 24/7 Support', 'code' => 'priority_support', 'value' => 'true', 'is_limited' => false],
                ],
            ],
            [
                'name' => 'Lifetime VIP',
                'slug' => 'lifetime-vip',
                'description' => 'One-time payment for lifetime unrestricted platform privileges.',
                'price' => 399.00,
                'signup_fee' => 0.00,
                'currency' => 'USD',
                'billing_interval' => 'lifetime',
                'interval_count' => 1,
                'trial_days' => 0,
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 4,
                'features' => [
                    ['name' => 'Unlimited Everything', 'code' => 'unlimited_all', 'value' => 'true', 'is_limited' => false],
                    ['name' => 'Unlimited Chat & Media', 'code' => 'chat_messages', 'value' => 'unlimited', 'is_limited' => false],
                    ['name' => 'Unlimited AI Queries', 'code' => 'ai_queries', 'value' => 'unlimited', 'is_limited' => false],
                    ['name' => 'Custom Profile Theme', 'code' => 'custom_themes', 'value' => 'true', 'is_limited' => false],
                    ['name' => 'Dedicated Account Manager', 'code' => 'dedicated_manager', 'value' => 'true', 'is_limited' => false],
                ],
            ],
        ];

        foreach ($plans as $planData) {
            $features = $planData['features'];
            unset($planData['features']);

            $plan = Plan::updateOrCreate(['slug' => $planData['slug']], $planData);

            $plan->features()->delete();
            foreach ($features as $idx => $feat) {
                PlanFeature::create(array_merge($feat, [
                    'plan_id' => $plan->id,
                    'sort_order' => $idx,
                ]));
            }
        }
    }
}
