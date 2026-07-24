<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\ActivityLog;
use App\Services\DashboardService;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $metrics = $this->dashboardService->getDashboardMetrics();

        // ── Top-4 Stat Cards ────────────────────────────────────────────────
        $totalUsers          = $metrics['users']['total_users'];
        $totalSubscribedUsers = $metrics['users']['subscribed_users'];
        $totalEvents         = $metrics['events']['total_events'];
        $totalPosts          = $metrics['posts']['total_posts'];

        // ── Secondary User Metrics ───────────────────────────────────────────
        $newUsers            = $metrics['users']['new_users'];
        $verifiedUsers       = $metrics['users']['verified_users'];
        $activeUsers         = $metrics['users']['active_users'];

        // ── Secondary Event / Post / Club Metrics ────────────────────────────
        $upcomingEvents      = $metrics['events']['upcoming_events'];
        $totalGoing          = $metrics['events']['total_going'];
        $totalInterested     = $metrics['events']['total_interested'];
        $publishedPosts      = $metrics['posts']['published_posts'];
        $newPostsMonth       = $metrics['posts']['new_posts_month'];
        $totalClubs          = $metrics['clubs']['total_clubs'];
        $totalClubMembers    = $metrics['clubs']['total_members'];

        // ── Recent Lists ─────────────────────────────────────────────────────
        $latestpostUsers     = $metrics['recent_users'];
        $recentEvents        = $metrics['recent_events'];
        $recentPosts         = $metrics['recent_posts'];

        // ── Chart Data ───────────────────────────────────────────────────────
        $signupCategories    = $metrics['monthly_signups']['categories'];
        $signupData          = $metrics['monthly_signups']['users'];
        $eventChartData      = $metrics['monthly_signups']['events'];
        $postChartData       = $metrics['monthly_signups']['posts'];

        // ── Pie Chart (active vs inactive) ───────────────────────────────────
        $activeCount         = $activeUsers;
        $inactiveCount       = max(0, $totalUsers - $activeCount);

        // ── Activity Log ─────────────────────────────────────────────────────
        $recentActivities    = ActivityLog::latest()->take(5)->get();

        return view('backend.dashboard', compact(
            // stat cards
            'totalUsers',
            'totalSubscribedUsers',
            'totalEvents',
            'totalPosts',
            // secondary
            'newUsers',
            'verifiedUsers',
            'activeUsers',
            'upcomingEvents',
            'totalGoing',
            'totalInterested',
            'publishedPosts',
            'newPostsMonth',
            'totalClubs',
            'totalClubMembers',
            // recent lists
            'latestpostUsers',
            'recentEvents',
            'recentPosts',
            // charts
            'metrics',
            'signupCategories',
            'signupData',
            'eventChartData',
            'postChartData',
            'activeCount',
            'inactiveCount',
            // misc
            'recentActivities'
        ));
    }
}
