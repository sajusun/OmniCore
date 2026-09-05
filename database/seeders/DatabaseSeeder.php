<?php

namespace Database\Seeders;

use App\Modules\Order\Database\Seeders\OrderModuleSeeder;
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
        ]);
    }
}
