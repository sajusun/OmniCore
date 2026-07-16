<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get aggregated user statistics.
     *
     * @return array
     */
    public function getUserStats(): array
    {
        return [
            'total_users'       => User::count(),
            'new_users'         => User::whereMonth('created_at', Carbon::now()->month)
                                        ->whereYear('created_at', Carbon::now()->year)
                                        ->count(),
            'verified_users'    => User::whereHas('verifications', function($query) {
                                            $query->where('purpose', 'email_verification')
                                                  ->where('status', 'verified')
                                                  ->whereNotNull('verified_at');
                                        })->count(),
            'active_users'      => User::where('last_activity_at', '>=', Carbon::now()->subMinutes(5))->count(),
        ];
    }

    /**
     * Get recent users.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentUsers(int $limit = 5)
    {
        return User::latest()->limit($limit)->get();
    }

    /**
     * Get all dashboard metrics grouped together.
     *
     * @return array
     */
    public function getDashboardMetrics(): array
    {
        $userStats = $this->getUserStats();
        $monthlySignups = $this->getMonthlySignups();

        return [
            'users' => $userStats,
            'recent_users' => $this->getRecentUsers(5),
            'monthly_signups' => $monthlySignups,
            'system' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'env' => config('app.env'),
            ]
        ];
    }

    /**
     * Get user signups count per month for the last 6 months.
     *
     * @return array
     */
    public function getMonthlySignups(): array
    {
        $categories = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $categories[] = $month->format('M');
            $data[] = User::whereMonth('created_at', $month->month)
                          ->whereYear('created_at', $month->year)
                          ->count();
        }

        return [
            'categories' => $categories,
            'data'       => $data,
        ];
    }
}
