<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ActivityLogController extends Controller
{
    protected ActivityLogService $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    /**
     * Display a listing of the activity logs.
     */
    public function index(Request $request)
    {
        // Enforce authorization
        Gate::authorize('viewAny', ActivityLog::class);

        $filters = $request->only(['search', 'event', 'module', 'date_from', 'date_to']);
        $activityLogs = $this->activityLogService->paginate(15, $filters);
        $events = $this->activityLogService->getUniqueEvents();
        $modules = $this->activityLogService->getUniqueModules();

        return view('admin.activity-logs.index', compact('activityLogs', 'events', 'modules', 'filters'));
    }

    /**
     * Display a listing of the activity logs scoped by a specific user.
     */
    public function userIndex(User $user, Request $request)
    {
        // Enforce authorization
        Gate::authorize('viewAny', ActivityLog::class);

        $filters = $request->only(['search', 'event', 'module', 'date_from', 'date_to']);
        $activityLogs = $this->activityLogService->paginateByUser($user, 15, $filters);
        $events = $this->activityLogService->getUniqueEvents();
        $modules = $this->activityLogService->getUniqueModules();

        return view('admin.activity-logs.index', compact('activityLogs', 'events', 'modules', 'filters', 'user'));
    }

    /**
     * Display the specified activity log detail.
     */
    public function show(ActivityLog $activityLog)
    {
        // Enforce authorization
        Gate::authorize('view', $activityLog);

        $activityLog = $this->activityLogService->getDetails($activityLog);

        return view('admin.activity-logs.show', compact('activityLog'));
    }
}
