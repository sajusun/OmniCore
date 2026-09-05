<?php

namespace App\Modules\Interaction\Traits;

use App\Models\User;
use App\Modules\Interaction\Models\Like;
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
        if (!$user) {
            return false;
        }

        $userId = $user instanceof User ? $user->id : $user;

        if ($this->relationLoaded('likes')) {
            return $this->likes->contains('user_id', $userId);
        }

        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * Toggle like state for a user.
     * Returns true if liked, false if unliked.
     */
    public function toggleLike(User|int $user, string $type = 'like'): array
    {
        $userId = $user instanceof User ? $user->id : $user;
        $existing = $this->likes()->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
            $this->decrementLikesCount();

            return [
                'liked' => false,
                'type' => null,
                'likes_count' => $this->getLikesCount(),
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
            'likes_count' => $this->getLikesCount(),
        ];
    }

    /**
     * Get current likes count.
     */
    public function getLikesCount(): int
    {
        if (isset($this->attributes['likes_count'])) {
            return (int) $this->attributes['likes_count'];
        }

        return $this->likes()->count();
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
