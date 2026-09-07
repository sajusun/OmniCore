<?php

namespace App\Modules\Interaction\Services;

use App\Models\User;
use App\Modules\Interaction\Models\View;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use InvalidArgumentException;

class ViewService
{
    /**
     * Resolve target model from type and ID.
     */
    public function resolveModel(string $type, int|string $id): Model
    {
        $morphMap = Relation::morphMap();
        $modelClass = $morphMap[$type] ?? (class_exists($type) ? $type : null);

        if (! $modelClass || ! class_exists($modelClass)) {
            throw new InvalidArgumentException("Invalid viewable type: {$type}");
        }

        $model = $modelClass::find($id);
        if (! $model) {
            throw new InvalidArgumentException("Target model not found for {$type} #{$id}");
        }

        return $model;
    }

    /**
     * Record a view/impression for a target model with anti-spam cooldown.
     */
    public function recordView(
        Model $model,
        User|int|null $user = null,
        ?string $ip = null,
        int $cooldownMinutes = 60,
        ?string $userAgent = null
    ): bool {
        $userId = $user instanceof User ? $user->id : $user;
        $cooldownThreshold = Carbon::now()->subMinutes($cooldownMinutes);

        $recentViewQuery = View::where('viewable_type', $model->getMorphClass())
            ->where('viewable_id', $model->getKey())
            ->where('created_at', '>=', $cooldownThreshold);

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

        View::create([
            'user_id' => $userId,
            'viewable_type' => $model->getMorphClass(),
            'viewable_id' => $model->getKey(),
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);

        if (array_key_exists('views_count', $model->getAttributes())) {
            $model->increment('views_count');
        }

        return true;
    }

    /**
     * Get view analytics statistics for a model over the last N days.
     */
    public function getDailyStats(Model $model, int $days = 7): array
    {
        $startDate = Carbon::now()->subDays($days)->startOfDay();

        $records = View::where('viewable_type', $model->getMorphClass())
            ->where('viewable_id', $model->getKey())
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
     * Get IDs and counts of top viewed items for a model type.
     */
    public function getTopViewed(string $type, int $limit = 10, int $days = 30): array
    {
        $morphMap = Relation::morphMap();
        $morphType = $morphMap[$type] ?? $type;
        $startDate = Carbon::now()->subDays($days)->startOfDay();

        return View::where('viewable_type', $morphType)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('viewable_id, count(*) as views_count, count(distinct coalesce(user_id, ip_address)) as unique_viewers')
            ->groupBy('viewable_id')
            ->orderByDesc('views_count')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
