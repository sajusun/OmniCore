<?php

namespace App\Modules\ActivityLog\Traits;

use App\Modules\ActivityLog\Observers\ActivityObserver;

trait LogsActivity
{
    /**
     * Boot the trait to automatically register the activity observer.
     */
    public static function bootLogsActivity(): void
    {
        static::observe(ActivityObserver::class);
    }
}
