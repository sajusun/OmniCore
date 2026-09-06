<?php

declare(strict_types=1);

namespace App\Modules\Affiliate\Database\Seeders;

use App\Models\User;
use App\Modules\Affiliate\Enums\AffiliateStatus;
use App\Modules\Affiliate\Enums\CommissionType;
use App\Modules\Affiliate\Models\AffiliateAccount;
use Illuminate\Database\Seeder;

class AffiliateSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if ($user) {
            AffiliateAccount::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'referral_code'        => 'VIP-PARTNER',
                    'custom_slug'          => 'vip-deal',
                    'commission_rate'      => 15.00,
                    'commission_type'      => CommissionType::PERCENTAGE->value,
                    'status'               => AffiliateStatus::ACTIVE->value,
                    'total_earnings'       => 150.00,
                    'paid_earnings'        => 50.00,
                    'current_balance'      => 100.00,
                    'lifetime_referrals'   => 45,
                    'lifetime_conversions' => 10,
                ]
            );
        }
    }
}
