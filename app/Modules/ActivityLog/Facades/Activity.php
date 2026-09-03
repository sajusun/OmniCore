<?php

namespace App\Modules\ActivityLog\Facades;

use App\Modules\ActivityLog\Services\ActivityLogService;
use Illuminate\Support\Facades\Facade;

class Activity extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return ActivityLogService::class;
    }
}
