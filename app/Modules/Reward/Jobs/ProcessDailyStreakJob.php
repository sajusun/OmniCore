<?php

namespace App\Modules\Reward\Jobs;

use App\Modules\Reward\Models\UserReward;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessDailyStreakJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /**
     * Execute the job to reset broken streaks.
     */
    public function handle(): void
    {
        try {
            $cutoff = Carbon::now()->subHours(48);

            $resetCount = UserReward::where('streak_days', '>', 0)
                ->where(function ($query) use ($cutoff) {
                    $query->whereNull('last_checkin_at')
                          ->orWhere('last_checkin_at', '<', $cutoff);
                })
                ->update([
                    'streak_days' => 0,
                ]);

            Log::info("Daily streak audit completed. Reset {$resetCount} inactive streaks.");
        } catch (Throwable $e) {
            Log::error('ProcessDailyStreakJob failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
