<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Modules\Auth\Models\Verification;
use App\Modules\Order\Models\Order;
use App\Modules\Post\Models\Post;
use App\Modules\Product\Models\Product;
use App\Modules\Review\Models\Review;
use App\Modules\Subscription\Models\Subscription;
use App\Modules\Ticket\Models\Ticket;
use App\Modules\Vendor\Models\VendorStore;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    /**
     * Cache duration for dashboard metrics (in seconds): 5 minutes.
     */
    public const CACHE_TTL_SECONDS = 300;

    // ─── User Stats ────────────────────────────────────────────────────────────

    public function getUserStats(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();

        return [
            'total_users'      => User::count(),
            'new_users'        => User::where('created_at', '>=', $startOfMonth)->count(),
            'verified_users'   => User::whereHas(
                'verifications',
                fn($q) =>
                $q->where('purpose', Verification::PURPOSE_EMAIL_VERIFICATION)
                    ->where('status', 'verified')
                    ->whereNotNull('verified_at')
            )->count(),
            'active_users'     => User::where('last_activity_at', '>=', $now->copy()->subMinutes(5))->count(),
            'subscribed_users' => User::where('is_subscribed', true)->count(),
        ];
    }

    // ─── E-Commerce & Orders Stats ─────────────────────────────────────────────

    public function getEcommerceStats(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();

        return [
            'total_orders'    => Order::count(),
            'total_products'  => Product::count(),
            'total_revenue'   => (float) Order::where('payment_status', 'paid')->sum('total_amount'),
            'pending_orders'  => Order::where('status', 'pending')->count(),
            'month_orders'    => Order::where('created_at', '>=', $startOfMonth)->count(),
        ];
    }

    // ─── Post & Content Stats ──────────────────────────────────────────────────

    public function getPostStats(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();

        return [
            'total_posts'     => Post::count(),
            'published_posts' => Post::where('status', 'published')->count(),
            'new_posts_month' => Post::where('created_at', '>=', $startOfMonth)->count(),
        ];
    }

    // ─── Operations & Support Stats ────────────────────────────────────────────

    public function getOperationsStats(): array
    {
        return [
            'total_tickets'   => Ticket::count(),
            'open_tickets'    => Ticket::whereIn('status', ['open', 'in_progress', 'pending'])->count(),
            'total_vendors'   => VendorStore::count(),
            'total_reviews'   => Review::count(),
            'subscriptions'   => Subscription::where('status', 'active')->count(),
        ];
    }

    // ─── Recent Records ────────────────────────────────────────────────────────

    public function getRecentUsers(int $limit = 5)
    {
        return User::latest()->limit($limit)->get(['id', 'name', 'email', 'avatar', 'created_at', 'status']);
    }

    public function getRecentOrders(int $limit = 5)
    {
        return Order::with('user:id,name,email')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getRecentPosts(int $limit = 5)
    {
        return Post::with('user:id,name')
            ->latest()
            ->limit($limit)
            ->get(['id', 'title', 'status', 'user_id', 'created_at']);
    }

    public function getRecentTickets(int $limit = 5)
    {
        return Ticket::with('user:id,name')
            ->latest()
            ->limit($limit)
            ->get();
    }

    // ─── Monthly Chart Data (Single Aggregate GROUP BY Queries) ───────────────

    public function getMonthlySignups(): array
    {
        $categories = [];
        $months     = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $key = $month->format('Y-m');
            $months[] = $key;
            $categories[] = $month->format('M Y');
        }

        $startDate = Carbon::now()->subMonths(5)->startOfMonth();

        // 4 single indexed aggregate queries with GROUP BY instead of 24 slow queries
        $userDataMap = User::where('created_at', '>=', $startDate)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as count")
            ->groupBy('ym')
            ->pluck('count', 'ym')
            ->toArray();

        $subDataMap = Subscription::where('created_at', '>=', $startDate)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as count")
            ->groupBy('ym')
            ->pluck('count', 'ym')
            ->toArray();

        $orderDataMap = Order::where('created_at', '>=', $startDate)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as count")
            ->groupBy('ym')
            ->pluck('count', 'ym')
            ->toArray();

        $postDataMap = Post::where('created_at', '>=', $startDate)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as count")
            ->groupBy('ym')
            ->pluck('count', 'ym')
            ->toArray();

        $userData  = array_map(fn($m) => (int) ($userDataMap[$m] ?? 0), $months);
        $subData   = array_map(fn($m) => (int) ($subDataMap[$m] ?? 0), $months);
        $orderData = array_map(fn($m) => (int) ($orderDataMap[$m] ?? 0), $months);
        $postData  = array_map(fn($m) => (int) ($postDataMap[$m] ?? 0), $months);

        return [
            'categories'    => $categories,
            'data'          => $userData,
            'users'         => $userData,
            'subscriptions' => $subData,
            'orders'        => $orderData,
            'events'        => $orderData, // backward compatibility alias
            'posts'         => $postData,
        ];
    }

    // ─── Master Metrics (Cached in Redis/Database Cache) ───────────────────────

    public function getDashboardMetrics(bool $fresh = false): array
    {
        if ($fresh) {
            $this->clearMetricsCache();
        }

        return Cache::remember('admin_dashboard_metrics', self::CACHE_TTL_SECONDS, function () {
            $ecommerceStats  = $this->getEcommerceStats();
            $operationsStats = $this->getOperationsStats();
            $userStats       = $this->getUserStats();
            $postStats       = $this->getPostStats();
            $recentOrders    = $this->getRecentOrders(5);

            return [
                'users'           => $userStats,
                'ecommerce'       => $ecommerceStats,
                'posts'           => $postStats,
                'operations'      => $operationsStats,
                // Aliases for dashboard view compatibility
                'events'          => [
                    'total_events'     => $ecommerceStats['total_orders'],
                    'upcoming_events'  => $ecommerceStats['pending_orders'],
                    'total_going'      => $operationsStats['total_tickets'],
                    'total_interested' => $operationsStats['open_tickets'],
                ],
                'clubs'           => [
                    'total_clubs'     => $operationsStats['total_vendors'],
                    'total_members'   => $operationsStats['total_reviews'],
                ],
                'vehicles'        => [
                    'total_vehicles'  => $ecommerceStats['total_products'],
                ],
                'recent_users'    => $this->getRecentUsers(5),
                'recent_events'   => $recentOrders,
                'recent_orders'   => $recentOrders,
                'recent_posts'    => $this->getRecentPosts(5),
                'recent_tickets'  => $this->getRecentTickets(5),
                'monthly_signups' => $this->getMonthlySignups(),
            ];
        });
    }

    /**
     * Invalidate dashboard metrics cache.
     */
    public function clearMetricsCache(): void
    {
        Cache::forget('admin_dashboard_metrics');
    }
}
