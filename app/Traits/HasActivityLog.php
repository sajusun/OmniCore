<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait HasActivityLog
{
    /**
     * Polymorphic relationship for activity logs.
     */
    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }

    /**
     * Helper to create a log entry for the model.
     */
    public function logActivity(string $title, ?string $description = null)
    {
        return $this->activityLogs()->create([
            'title' => $title,
            'description' => $description,
        ]);
    }

    /**
     * Get latest activity logs (default 5).
     */
    public function latestActivities(int $limit = 7)
    {
        return $this->activityLogs()->latest()->take($limit)->get();
    }
}
