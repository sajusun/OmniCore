<?php

namespace App\Http\Controllers\Web\Backend;

use App\Modules\ActivityLog\Models\ActivityLog;
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
        $totalUsers           = $metrics['users']['total_users'];
        $totalSubscribedUsers = $metrics['users']['subscribed_users'];
        $totalOrders          = $metrics['ecommerce']['total_orders'];
        $totalEvents          = $totalOrders; // backward compatibility
        $totalPosts           = $metrics['posts']['total_posts'];

        // ── Secondary User Metrics ───────────────────────────────────────────
        $newUsers             = $metrics['users']['new_users'];
        $verifiedUsers        = $metrics['users']['verified_users'];
        $activeUsers          = $metrics['users']['active_users'];

        // ── Secondary Ecommerce & Operations Metrics ─────────────────────────
        $pendingOrders        = $metrics['ecommerce']['pending_orders'];
        $upcomingEvents       = $pendingOrders;
        $totalProducts        = $metrics['ecommerce']['total_products'];
        $totalVehicles        = $totalProducts;
        $totalVendors         = $metrics['operations']['total_vendors'];
        $totalClubs           = $totalVendors;
        $totalReviews         = $metrics['operations']['total_reviews'];
        $totalClubMembers     = $totalReviews;
        $totalTickets         = $metrics['operations']['total_tickets'];
        $totalGoing           = $totalTickets;
        $totalInterested      = $metrics['operations']['open_tickets'];
        $publishedPosts       = $metrics['posts']['published_posts'];
        $newPostsMonth        = $metrics['posts']['new_posts_month'];

        // ── Recent Lists ─────────────────────────────────────────────────────
        $latestpostUsers      = $metrics['recent_users'];
        $recentEvents         = $metrics['recent_events'];
        $recentOrders         = $metrics['recent_orders'];
        $recentPosts          = $metrics['recent_posts'];
        $recentTickets        = $metrics['recent_tickets'];

        // ── Chart Data ───────────────────────────────────────────────────────
        $signupCategories       = $metrics['monthly_signups']['categories'];
        $signupData             = $metrics['monthly_signups']['users'];
        $subscriptionChartData  = $metrics['monthly_signups']['subscriptions'];
        $eventChartData         = $metrics['monthly_signups']['orders'] ?? $metrics['monthly_signups']['events'];
        $postChartData          = $metrics['monthly_signups']['posts'];

        // ── Pie / Donut Chart Datasets ────────────────────────────────────────
        $activeCount            = $activeUsers;
        $inactiveCount          = max(0, $totalUsers - $activeCount);
        $freeUsersCount         = max(0, $totalUsers - $totalSubscribedUsers);
        $unverifiedUsersCount   = max(0, $totalUsers - $verifiedUsers);

        // ── Activity Log ─────────────────────────────────────────────────────
        $recentActivities       = ActivityLog::latest()->take(5)->get();

        return view('backend.dashboard', compact(
            // stat cards
            'totalUsers',
            'totalSubscribedUsers',
            'totalOrders',
            'totalEvents',
            'totalPosts',
            'totalProducts',
            'totalVehicles',
            'totalVendors',
            'totalClubs',
            'totalReviews',
            'totalClubMembers',
            'totalTickets',
            'pendingOrders',
            // secondary
            'newUsers',
            'verifiedUsers',
            'activeUsers',
            'upcomingEvents',
            'totalGoing',
            'totalInterested',
            'publishedPosts',
            'newPostsMonth',
            'freeUsersCount',
            'unverifiedUsersCount',
            // recent lists
            'latestpostUsers',
            'recentEvents',
            'recentOrders',
            'recentPosts',
            'recentTickets',
            // charts
            'metrics',
            'signupCategories',
            'signupData',
            'subscriptionChartData',
            'eventChartData',
            'postChartData',
            'activeCount',
            'inactiveCount',
            // misc
            'recentActivities'
        ));
    }
}
