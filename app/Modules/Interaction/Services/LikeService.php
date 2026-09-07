<?php

namespace App\Modules\Interaction\Services;

use App\Models\User;
use App\Modules\Interaction\Models\Like;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use InvalidArgumentException;

class LikeService
{
    /**
     * Resolve target model from type and ID.
     */
    public function resolveModel(string $type, int|string $id): Model
    {
        $morphMap = Relation::morphMap();
        $modelClass = $morphMap[$type] ?? (class_exists($type) ? $type : null);

        if (! $modelClass || ! class_exists($modelClass)) {
            throw new InvalidArgumentException("Invalid likeable type: {$type}");
        }

        $model = $modelClass::find($id);
        if (! $model) {
            throw new InvalidArgumentException("Target model not found for {$type} #{$id}");
        }

        return $model;
    }

    /**
     * Explicitly like a model.
     */
    public function like(Model $model, User|int $user, string $type = 'like'): array
    {
        $userId = $user instanceof User ? $user->id : $user;

        $existing = Like::where('likeable_type', $model->getMorphClass())
            ->where('likeable_id', $model->getKey())
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            if ($existing->type !== $type) {
                $existing->update(['type' => $type]);
            }
        } else {
            Like::create([
                'user_id' => $userId,
                'likeable_type' => $model->getMorphClass(),
                'likeable_id' => $model->getKey(),
                'type' => $type,
            ]);

            if (array_key_exists('likes_count', $model->getAttributes())) {
                $model->increment('likes_count');
            }
        }

        $totalLikes = Like::where('likeable_type', $model->getMorphClass())
            ->where('likeable_id', $model->getKey())
            ->count();

        return [
            'liked' => true,
            'type' => $type,
            'likes_count' => $totalLikes,
        ];
    }

    /**
     * Explicitly unlike a model.
     */
    public function unlike(Model $model, User|int $user): array
    {
        $userId = $user instanceof User ? $user->id : $user;

        $existing = Like::where('likeable_type', $model->getMorphClass())
            ->where('likeable_id', $model->getKey())
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            $existing->delete();

            if (array_key_exists('likes_count', $model->getAttributes()) && $model->likes_count > 0) {
                $model->decrement('likes_count');
            }
        }

        $totalLikes = Like::where('likeable_type', $model->getMorphClass())
            ->where('likeable_id', $model->getKey())
            ->count();

        return [
            'liked' => false,
            'type' => null,
            'likes_count' => $totalLikes,
        ];
    }

    /**
     * Toggle like state on any model or comment.
     */
    public function toggleLike(Model $model, User|int $user, string $type = 'like'): array
    {
        $userId = $user instanceof User ? $user->id : $user;

        $existing = Like::where('likeable_type', $model->getMorphClass())
            ->where('likeable_id', $model->getKey())
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            return $this->unlike($model, $user);
        }

        return $this->like($model, $user, $type);
    }

    /**
     * Check if user has liked the model.
     */
    public function isLiked(Model $model, User|int|null $user): bool
    {
        if (! $user) {
            return false;
        }

        $userId = $user instanceof User ? $user->id : $user;

        return Like::where('likeable_type', $model->getMorphClass())
            ->where('likeable_id', $model->getKey())
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Get paginated list of users who liked this model.
     */
    public function getLikers(Model $model, int $perPage = 20): LengthAwarePaginator
    {
        return Like::where('likeable_type', $model->getMorphClass())
            ->where('likeable_id', $model->getKey())
            ->with('user:id,name,avatar,email')
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Get breakdown of reactions by type.
     */
    public function getLikesBreakdown(Model $model): array
    {
        return Like::where('likeable_type', $model->getMorphClass())
            ->where('likeable_id', $model->getKey())
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }
}
