<?php

namespace Database\Seeders;

use App\Enums\VehicleRequiredEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleRequirementSeeder extends Seeder
{
    public function run(): void
    {
        foreach (VehicleRequiredEnum::cases() as $case) {
            DB::table('vehicle_requirements')->updateOrInsert(
                ['id' => $case->value],
                [
                    'name'       => $case->label(),
                    'slug'       => strtolower(str_replace(' ', '_', $case->label())),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
