<?php

namespace App\Modules\Interaction\Services;

use App\Models\User;
use App\Modules\Interaction\Models\View;
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

        if (!$modelClass || !class_exists($modelClass)) {
            throw new InvalidArgumentException("Invalid viewable type: {$type}");
        }

        $model = $modelClass::find($id);
        if (!$model) {
            throw new InvalidArgumentException("Target model not found for {$type} #{$id}");
        }

        return $model;
    }

    /**
     * Record a view for a model.
     */
    public function recordView(
        Model $model,
        ?User $user = null,
        ?string $ip = null,
        ?string $userAgent = null,
        int $cooldownMinutes = 60
    ): array {
        if (method_exists($model, 'recordView')) {
            $recorded = $model->recordView($user, $ip, $cooldownMinutes, $userAgent);
        } else {
            $userId = $user?->id;
            $hasRecent = View::where('viewable_type', $model->getMorphClass())
                ->where('viewable_id', $model->getKey())
                ->when($userId, fn($q) => $q->where('user_id', $userId))
                ->when(!$userId && $ip, fn($q) => $q->where('ip_address', $ip))
                ->where('created_at', '>=', now()->subMinutes($cooldownMinutes))
                ->exists();

            if (!$hasRecent) {
                View::create([
                    'viewable_type' => $model->getMorphClass(),
                    'viewable_id' => $model->getKey(),
                    'user_id' => $userId,
                    'ip_address' => $ip,
                    'user_agent' => $userAgent,
                ]);
                $recorded = true;
            } else {
                $recorded = false;
            }
        }

        $totalViews = method_exists($model, 'viewsCount')
            ? $model->viewsCount()
            : View::where('viewable_type', $model->getMorphClass())->where('viewable_id', $model->getKey())->count();

        $uniqueViews = method_exists($model, 'uniqueViewsCount')
            ? $model->uniqueViewsCount()
            : View::where('viewable_type', $model->getMorphClass())
                ->where('viewable_id', $model->getKey())
                ->selectRaw('COUNT(DISTINCT COALESCE(user_id, ip_address)) as total')
                ->value('total') ?? 0;

        return [
            'recorded' => $recorded,
            'total_views' => $totalViews,
            'unique_views' => $uniqueViews,
        ];
    }

    /**
     * Get view stats for a model.
     */
    public function getViewStats(Model $model): array
    {
        $totalViews = method_exists($model, 'viewsCount')
            ? $model->viewsCount()
            : View::where('viewable_type', $model->getMorphClass())->where('viewable_id', $model->getKey())->count();

        $uniqueViews = method_exists($model, 'uniqueViewsCount')
            ? $model->uniqueViewsCount()
            : View::where('viewable_type', $model->getMorphClass())
                ->where('viewable_id', $model->getKey())
                ->selectRaw('COUNT(DISTINCT COALESCE(user_id, ip_address)) as total')
                ->value('total') ?? 0;

        return [
            'total_views' => $totalViews,
            'unique_views' => $uniqueViews,
        ];
    }
}
