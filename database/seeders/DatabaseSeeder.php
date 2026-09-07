<?php

namespace Database\Seeders;

use App\Modules\AI\Database\Seeders\AiSeeder;
use App\Modules\Affiliate\Database\Seeders\AffiliateSeeder;
use App\Modules\CMS\Database\Seeders\CMSModuleSeeder;
use App\Modules\Chat\Database\Seeders\ChatModuleSeeder;
use App\Modules\Order\Database\Seeders\OrderModuleSeeder;
use App\Modules\Payment\Database\Seeders\PaymentGatewaySeeder;
use App\Modules\Post\Database\Seeders\PostModuleSeeder;
use App\Modules\Product\Database\Seeders\ProductModuleSeeder;
use App\Modules\Review\Database\Seeders\ReviewSeeder;
use App\Modules\Reward\Database\Seeders\RewardSeeder;
use App\Modules\Subscription\Database\Seeders\SubscriptionPlanSeeder;
use App\Modules\Ticket\Database\Seeders\TicketSeeder;
use App\Modules\Vendor\Database\Seeders\VendorSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProductModuleSeeder::class,
            OrderModuleSeeder::class,
            PostModuleSeeder::class,
            ChatModuleSeeder::class,
            CMSModuleSeeder::class,
            PaymentGatewaySeeder::class,
            SubscriptionPlanSeeder::class,
            RewardSeeder::class,
            TicketSeeder::class,
            ReviewSeeder::class,
            AffiliateSeeder::class,
            VendorSeeder::class,
            AiSeeder::class,
        ]);
    }
}
