<?php

namespace App\Modules\ActivityLog\Policies;

use App\Models\User;
use App\Modules\ActivityLog\Models\ActivityLog;

class ActivityLogPolicy
{
    /**
     * Determine whether the user can view any activity logs.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super_admin']);
    }

    /**
     * Determine whether the user can view a specific activity log.
     */
    public function view(User $user, ActivityLog $activityLog): bool
    {
        return $user->hasAnyRole(['admin', 'super_admin']);
    }
}
