<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Club;
use App\Models\Post;
use App\Models\User;
use App\Models\Event;
use App\Models\Verification;

class DashboardService
{
    // ─── User Stats ────────────────────────────────────────────────────────────

    public function getUserStats(): array
    {
        $now = Carbon::now();

        return [
            'total_users'      => User::count(),
            'new_users'        => User::whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->count(),
            'verified_users'   => User::whereHas(
                'verifications',
                fn($q) =>
                $q->where('purpose', Verification::PURPOSE_EMAIL_VERIFICATION)
                    ->where('status', 'verified')
                    ->whereNotNull('verified_at')
            )->count(),
            'active_users'     => User::where('last_activity_at', '>=', $now->subMinutes(5))->count(),
            'subscribed_users' => User::where('is_subscribed', true)->count(),
        ];
    }

    // ─── Event Stats ───────────────────────────────────────────────────────────

    public function getEventStats(): array
    {
        $now = Carbon::now();

        return [
            'total_events'     => Event::count(),
            'upcoming_events'  => Event::where('event_date', '>=', $now->toDateString())
                ->where('status', 'published')
                ->count(),
            'total_going'      => \App\Models\EventRsvp::where('status', 'going')->count(),
            'total_interested' => \App\Models\EventRsvp::where('status', 'interested')->count(),
        ];
    }

    // ─── Post Stats ────────────────────────────────────────────────────────────

    public function getPostStats(): array
    {
        $now = Carbon::now();

        return [
            'total_posts'     => Post::count(),
            'published_posts' => Post::where('status', 'published')->count(),
            'new_posts_month' => Post::whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->count(),
        ];
    }

    // ─── Club Stats ────────────────────────────────────────────────────────────

    public function getClubStats(): array
    {
        return [
            'total_clubs'     => Club::count(),
            'total_members'   => \App\Models\ClubMember::where('status', 'approved')->count(),
        ];
    }

    // ─── Recent Records ────────────────────────────────────────────────────────

    public function getRecentUsers(int $limit = 5)
    {
        return User::latest()->limit($limit)->get(['id', 'name', 'email', 'avatar', 'created_at', 'status']);
    }

    public function getRecentEvents(int $limit = 5)
    {
        return Event::with('user:id,name')
            ->latest()
            ->limit($limit)
            ->get(['id', 'title', 'event_type', 'event_date', 'status', 'user_id', 'location']);
    }

    public function getRecentPosts(int $limit = 5)
    {
        return Post::with('user:id,name')
            ->latest()
            ->limit($limit)
            ->get(['id', 'title', 'status', 'user_id', 'created_at']);
    }

    // ─── Monthly Chart Data ────────────────────────────────────────────────────

    public function getMonthlySignups(): array
    {
        $categories = [];
        $userData   = [];
        $eventData  = [];
        $postData   = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $categories[] = $month->format('M Y');

            $userData[]  = User::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
            $eventData[] = Event::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
            $postData[]  = Post::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
        }

        return [
            'categories' => $categories,
            'data'       => $userData,       // backward-compat (signup line)
            'users'      => $userData,
            'events'     => $eventData,
            'posts'      => $postData,
        ];
    }

    // ─── Master Metrics ────────────────────────────────────────────────────────

    public function getDashboardMetrics(): array
    {
        return [
            'users'           => $this->getUserStats(),
            'events'          => $this->getEventStats(),
            'posts'           => $this->getPostStats(),
            'clubs'           => $this->getClubStats(),
            'recent_users'    => $this->getRecentUsers(5),
            'recent_events'   => $this->getRecentEvents(5),
            'recent_posts'    => $this->getRecentPosts(5),
            'monthly_signups' => $this->getMonthlySignups(),
            // 'system'          => [
            //     'php_version'     => PHP_VERSION,
            //     'laravel_version' => app()->version(),
            //     'env'             => config('app.env'),
            // ],
        ];
    }
}
