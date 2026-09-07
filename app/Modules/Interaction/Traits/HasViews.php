<?php

namespace App\Modules\Interaction\Traits;

use App\Models\User;
use App\Modules\Interaction\Models\View;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasViews
{
    /**
     * Get all views recorded for this model.
     */
    public function views(): MorphMany
    {
        return $this->morphMany(View::class, 'viewable');
    }

    /**
     * Record a view/impression with anti-spam cooldown.
     *
     * @param  User|int|null  $user  Authenticated user instance or ID
     * @param  string|null  $ip  Visitor IP address
     * @param  int  $cooldownMinutes  Minimum minutes required before recording another view from same user/IP
     * @param  string|null  $userAgent  Visitor browser User-Agent
     * @return bool True if a new view was recorded, false if ignored due to cooldown
     */
    public function recordView(
        User|int|null $user = null,
        ?string $ip = null,
        int $cooldownMinutes = 60,
        ?string $userAgent = null
    ): bool {
        $userId = $user instanceof User ? $user->id : $user;

        // Check recent view cooldown
        $cooldownThreshold = Carbon::now()->subMinutes($cooldownMinutes);
        $recentViewQuery = $this->views()->where('created_at', '>=', $cooldownThreshold);

        if ($userId) {
            $hasRecent = $recentViewQuery->where('user_id', $userId)->exists();
        } elseif ($ip) {
            $hasRecent = $recentViewQuery->where('ip_address', $ip)->exists();
        } else {
            $hasRecent = false;
        }

        if ($hasRecent) {
            return false;
        }

        // Record new view
        $this->views()->create([
            'user_id' => $userId,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);

        // Increment cached views_count if column exists on model
        $this->incrementViewsCount();

        return true;
    }

    /**
     * Get total view count.
     */
    public function viewsCount(): int
    {
        if (isset($this->attributes['views_count'])) {
            return (int) $this->attributes['views_count'];
        }

        return $this->views()->count();
    }

    /**
     * Get unique viewers count (by distinct user_id or IP).
     */
    public function uniqueViewsCount(): int
    {
        return $this->views()
            ->selectRaw('COUNT(DISTINCT COALESCE(user_id, ip_address)) as total')
            ->value('total') ?? 0;
    }

    /**
     * Check if a specific user has viewed this model.
     */
    public function isViewedBy(User|int|null $user): bool
    {
        if (! $user) {
            return false;
        }

        $userId = $user instanceof User ? $user->id : $user;

        return $this->views()->where('user_id', $userId)->exists();
    }

    /**
     * Get daily views breakdown over the last N days (for charts/analytics).
     */
    public function viewsStats(int $days = 7): array
    {
        $startDate = Carbon::now()->subDays($days)->startOfDay();

        $records = $this->views()
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total_views, COUNT(DISTINCT COALESCE(user_id, ip_address)) as unique_views')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');

        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $dateStr = Carbon::now()->subDays($i)->format('Y-m-d');
            $row = $records->get($dateStr);

            $result[] = [
                'date' => $dateStr,
                'total_views' => $row ? (int) $row->total_views : 0,
                'unique_views' => $row ? (int) $row->unique_views : 0,
            ];
        }

        return $result;
    }

    /**
     * Increment cached views_count on model.
     */
    protected function incrementViewsCount(): void
    {
        if (in_array('views_count', $this->getFillable()) || array_key_exists('views_count', $this->attributes)) {
            $this->increment('views_count');
        }
    }
}
