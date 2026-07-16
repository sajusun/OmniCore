<?php

namespace App\Traits;

use App\Observers\ActivityObserver;

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
