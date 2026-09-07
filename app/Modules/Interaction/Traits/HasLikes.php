<?php

namespace App\Modules\Interaction\Traits;

use App\Models\User;
use App\Modules\Interaction\Models\Like;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasLikes
{
    /**
     * Get all likes associated with this model.
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Check if a specific user has liked this model.
     */
    public function isLikedBy(User|int|null $user): bool
    {
        if (! $user) {
            return false;
        }

        $userId = $user instanceof User ? $user->id : $user;

        if ($this->relationLoaded('likes')) {
            return $this->likes->contains('user_id', $userId);
        }

        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * Explicitly like this model.
     */
    public function like(User|int $user, string $type = 'like'): array
    {
        $userId = $user instanceof User ? $user->id : $user;
        $existing = $this->likes()->where('user_id', $userId)->first();

        if ($existing) {
            if ($existing->type !== $type) {
                $existing->update(['type' => $type]);
            }

            return [
                'liked' => true,
                'type' => $type,
                'likes_count' => $this->likesCount(),
            ];
        }

        $this->likes()->create([
            'user_id' => $userId,
            'type' => $type,
        ]);
        $this->incrementLikesCount();

        return [
            'liked' => true,
            'type' => $type,
            'likes_count' => $this->likesCount(),
        ];
    }

    /**
     * Explicitly unlike this model.
     */
    public function unlike(User|int $user): array
    {
        $userId = $user instanceof User ? $user->id : $user;
        $existing = $this->likes()->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
            $this->decrementLikesCount();
        }

        return [
            'liked' => false,
            'type' => null,
            'likes_count' => $this->likesCount(),
        ];
    }

    /**
     * Toggle like state for a user.
     * Returns array containing liked status, type, and total count.
     */
    public function toggleLike(User|int $user, string $type = 'like'): array
    {
        $userId = $user instanceof User ? $user->id : $user;
        $existing = $this->likes()->where('user_id', $userId)->first();

        if ($existing) {
            return $this->unlike($user);
        }

        return $this->like($user, $type);
    }

    /**
     * Get total likes count.
     */
    public function likesCount(): int
    {
        return $this->getLikesCount();
    }

    /**
     * Get current likes count (checks cached attribute first).
     */
    public function getLikesCount(): int
    {
        if (isset($this->attributes['likes_count'])) {
            return (int) $this->attributes['likes_count'];
        }

        return $this->likes()->count();
    }

    /**
     * Get count of likes by specific reaction type, or array breakdown of all reaction types.
     */
    public function likesByType(?string $type = null): int|array
    {
        if ($type !== null) {
            return $this->likes()->where('type', $type)->count();
        }

        return $this->likes()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }

    /**
     * Get recent users who liked this model.
     */
    public function likers(int $limit = 20): Collection
    {
        $userIds = $this->likes()
            ->latest('id')
            ->limit($limit)
            ->pluck('user_id');

        return User::whereIn('id', $userIds)->get();
    }

    /**
     * Helper to safely increment cached likes_count if column exists.
     */
    protected function incrementLikesCount(): void
    {
        if (in_array('likes_count', $this->getFillable()) || array_key_exists('likes_count', $this->attributes)) {
            $this->increment('likes_count');
        }
    }

    /**
     * Helper to safely decrement cached likes_count if column exists.
     */
    protected function decrementLikesCount(): void
    {
        if (in_array('likes_count', $this->getFillable()) || array_key_exists('likes_count', $this->attributes)) {
            if ($this->likes_count > 0) {
                $this->decrement('likes_count');
            }
        }
    }
}
