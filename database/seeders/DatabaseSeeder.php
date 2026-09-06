<?php

namespace Database\Seeders;

use App\Modules\CMS\Database\Seeders\CMSModuleSeeder;
use App\Modules\Chat\Database\Seeders\ChatModuleSeeder;
use App\Modules\Order\Database\Seeders\OrderModuleSeeder;
use App\Modules\Post\Database\Seeders\PostModuleSeeder;
use App\Modules\Product\Database\Seeders\ProductModuleSeeder;
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
            \App\Modules\Payment\Database\Seeders\PaymentSeeder::class,
            \App\Modules\Subscription\Database\Seeders\SubscriptionSeeder::class,
            \App\Modules\Reward\Database\Seeders\RewardSeeder::class,
            \App\Modules\Ticket\Database\Seeders\TicketSeeder::class,
            \App\Modules\Review\Database\Seeders\ReviewSeeder::class,
            \App\Modules\Affiliate\Database\Seeders\AffiliateSeeder::class,
            \App\Modules\Vendor\Database\Seeders\VendorSeeder::class,
            \App\Modules\AI\Database\Seeders\AiSeeder::class,
        ]);
    }
}
