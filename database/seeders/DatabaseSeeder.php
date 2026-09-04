<?php

namespace Database\Seeders;

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
            EventTypeSeeder::class,
            VehicleRequirementSeeder::class,
            \App\Modules\Product\Database\Seeders\ProductModuleSeeder::class,
            \App\Modules\Order\Database\Seeders\OrderModuleSeeder::class,
        ]);
    }
}

