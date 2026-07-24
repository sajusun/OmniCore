<?php

namespace Database\Seeders;

use App\Enums\EventTypeEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (EventTypeEnum::cases() as $case) {
            DB::table('event_types')->updateOrInsert(
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
