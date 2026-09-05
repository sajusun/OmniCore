<?php

namespace App\Modules\Interaction\Traits;

use App\Models\User;
use App\Modules\Interaction\Models\Bookmark;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasBookmarks
{
    /**
     * Get all bookmarks for this model.
     */
    public function bookmarks(): MorphMany
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    /**
     * Toggle bookmark/save state for a user.
     *
     * @param User|int $user User instance or ID
     * @param string $collection Collection name ('default', 'wishlist', 'favorite', 'saved')
     * @return array Result containing boolean bookmarked state and total count
     */
    public function toggleBookmark(User|int $user, string $collection = 'default'): array
    {
        $userId = $user instanceof User ? $user->id : $user;

        $existing = $this->bookmarks()
            ->where('user_id', $userId)
            ->where('collection', $collection)
            ->first();

        if ($existing) {
            $existing->delete();

            $total = $this->bookmarks()->where('collection', $collection)->count();

            return [
                'bookmarked' => false,
                'collection' => $collection,
                'bookmarks_count' => $total,
            ];
        }

        $this->bookmarks()->create([
            'user_id' => $userId,
            'collection' => $collection,
        ]);

        $total = $this->bookmarks()->where('collection', $collection)->count();

        return [
            'bookmarked' => true,
            'collection' => $collection,
            'bookmarks_count' => $total,
        ];
    }

    /**
     * Check if a specific user has bookmarked/saved this model.
     */
    public function isBookmarkedBy(User|int|null $user, string $collection = 'default'): bool
    {
        if (!$user) {
            return false;
        }

        $userId = $user instanceof User ? $user->id : $user;

        if ($this->relationLoaded('bookmarks')) {
            return $this->bookmarks
                ->where('user_id', $userId)
                ->where('collection', $collection)
                ->isNotEmpty();
        }

        return $this->bookmarks()
            ->where('user_id', $userId)
            ->where('collection', $collection)
            ->exists();
    }

    /**
     * Get total count of bookmarks for a specific collection.
     */
    public function bookmarksCount(string $collection = 'default'): int
    {
        return $this->bookmarks()->where('collection', $collection)->count();
    }
}
