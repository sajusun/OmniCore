<?php

namespace App\Modules\Interaction\Traits;

use App\Models\User;
use App\Modules\Interaction\Models\Bookmark;
use Illuminate\Database\Eloquent\Collection;
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
     * Explicitly save/bookmark this model in a collection.
     */
    public function bookmark(User|int $user, string $collection = 'default'): array
    {
        $userId = $user instanceof User ? $user->id : $user;

        $existing = $this->bookmarks()
            ->where('user_id', $userId)
            ->where('collection', $collection)
            ->first();

        if (! $existing) {
            $this->bookmarks()->create([
                'user_id' => $userId,
                'collection' => $collection,
            ]);
        }

        $total = $this->bookmarks()->where('collection', $collection)->count();

        return [
            'bookmarked' => true,
            'collection' => $collection,
            'bookmarks_count' => $total,
        ];
    }

    /**
     * Explicitly remove bookmark from a collection.
     */
    public function unbookmark(User|int $user, string $collection = 'default'): array
    {
        $userId = $user instanceof User ? $user->id : $user;

        $this->bookmarks()
            ->where('user_id', $userId)
            ->where('collection', $collection)
            ->delete();

        $total = $this->bookmarks()->where('collection', $collection)->count();

        return [
            'bookmarked' => false,
            'collection' => $collection,
            'bookmarks_count' => $total,
        ];
    }

    /**
     * Toggle bookmark/save state for a user in a specified collection.
     */
    public function toggleBookmark(User|int $user, string $collection = 'default'): array
    {
        $userId = $user instanceof User ? $user->id : $user;

        $existing = $this->bookmarks()
            ->where('user_id', $userId)
            ->where('collection', $collection)
            ->first();

        if ($existing) {
            return $this->unbookmark($user, $collection);
        }

        return $this->bookmark($user, $collection);
    }

    /**
     * Check if a specific user has bookmarked/saved this model.
     */
    public function isBookmarkedBy(User|int|null $user, string $collection = 'default'): bool
    {
        if (! $user) {
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
     * Get total count of bookmarks for a specific collection or all collections.
     */
    public function bookmarksCount(?string $collection = null): int
    {
        if ($collection !== null) {
            return $this->bookmarks()->where('collection', $collection)->count();
        }

        return $this->bookmarks()->count();
    }

    /**
     * Get recent users who saved/bookmarked this model.
     */
    public function bookmarkers(string $collection = 'default', int $limit = 20): Collection
    {
        $userIds = $this->bookmarks()
            ->where('collection', $collection)
            ->latest('id')
            ->limit($limit)
            ->pluck('user_id');

        return User::whereIn('id', $userIds)->get();
    }

    /**
     * Get count breakdown by collection names.
     */
    public function collectionsSummary(): array
    {
        return $this->bookmarks()
            ->selectRaw('collection, count(*) as count')
            ->groupBy('collection')
            ->pluck('count', 'collection')
            ->toArray();
    }
}
