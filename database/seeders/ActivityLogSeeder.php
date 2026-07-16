<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityLog;
use Carbon\Carbon;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 sample activity logs
        for ($i = 1; $i <= 10; $i++) {
            ActivityLog::create([
                'title' => 'Sample Activity ' . $i,
                'description' => 'This is a description for activity log number ' . $i,
                'loggable_id' => 0, // No specific model attached
                'loggable_type' => 'system',
                'created_at' => Carbon::now()->subDays(10 - $i),
                'updated_at' => Carbon::now()->subDays(10 - $i),
            ]);
        }
    }
}
