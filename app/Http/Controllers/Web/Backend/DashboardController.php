<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $metrics = $this->dashboardService->getDashboardMetrics();

        $totalUsers = $metrics['users']['total_users'];
        $newUsers = $metrics['users']['new_users'];
        $verifiedUsers = $metrics['users']['verified_users'];
        $activeUsers = $metrics['users']['active_users'];

        $latestpostUsers = $metrics['recent_users'];
        $latestPropertyListings = collect();
        $latestReviews = collect();

        // Chart Data
        $signupCategories = $metrics['monthly_signups']['categories'];
        $signupData = $metrics['monthly_signups']['data'];

        // Pie Chart series (Active Users vs Inactive Users)
        $activeCount = $metrics['users']['active_users'];
        $inactiveCount = max(0, $totalUsers - $activeCount);

        // Recent Activity Logs (Spatie Activitylog)
        $recentActivities = ActivityLog::latest()->take(5)->get();

        return view('backend.dashboard', compact(
            'totalUsers',
            'newUsers',
            'verifiedUsers',
            'activeUsers',
            'latestpostUsers',
            'latestPropertyListings',
            'latestReviews',
            'metrics',
            'signupCategories',
            'signupData',
            'activeCount',
            'inactiveCount',
            'recentActivities'
        ));
    }
}
