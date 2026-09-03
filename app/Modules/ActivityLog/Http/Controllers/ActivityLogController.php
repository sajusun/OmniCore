<?php

namespace App\Modules\ActivityLog\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\ActivityLog\Models\ActivityLog;
use App\Modules\ActivityLog\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ActivityLogController extends Controller
{
    public function __construct(protected ActivityLogService $activityLogService) {}

    /**
     * Display a listing of the activity logs.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', ActivityLog::class);

        $filters      = $request->only(['search', 'event', 'module', 'date_from', 'date_to']);
        $activityLogs = $this->activityLogService->paginate(15, $filters);
        $events       = $this->activityLogService->getUniqueEvents();
        $modules      = $this->activityLogService->getUniqueModules();

        $viewName = view()->exists('activity_log::backend.index') ? 'activity_log::backend.index' : 'admin.activity-logs.index';

        return view($viewName, compact('activityLogs', 'events', 'modules', 'filters'));
    }

    /**
     * Display a listing of the activity logs scoped by a specific user.
     */
    public function userIndex(User $user, Request $request)
    {
        Gate::authorize('viewAny', ActivityLog::class);

        $filters      = $request->only(['search', 'event', 'module', 'date_from', 'date_to']);
        $activityLogs = $this->activityLogService->paginateByUser($user, 15, $filters);
        $events       = $this->activityLogService->getUniqueEvents();
        $modules      = $this->activityLogService->getUniqueModules();

        $viewName = view()->exists('activity_log::backend.index') ? 'activity_log::backend.index' : 'admin.activity-logs.index';

        return view($viewName, compact('activityLogs', 'events', 'modules', 'filters', 'user'));
    }

    /**
     * Display the specified activity log detail.
     */
    public function show(ActivityLog $activityLog)
    {
        Gate::authorize('view', $activityLog);

        $activityLog = $this->activityLogService->getDetails($activityLog);

        $viewName = view()->exists('activity_log::backend.show') ? 'activity_log::backend.show' : 'admin.activity-logs.show';

        return view($viewName, compact('activityLog'));
    }
}
