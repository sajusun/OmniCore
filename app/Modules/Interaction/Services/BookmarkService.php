<?php

namespace App\Modules\Interaction\Services;

use App\Models\User;
use App\Modules\Interaction\Models\Bookmark;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use InvalidArgumentException;

class BookmarkService
{
    /**
     * Resolve target model from type and ID.
     */
    public function resolveModel(string $type, int|string $id): Model
    {
        $morphMap = Relation::morphMap();
        $modelClass = $morphMap[$type] ?? (class_exists($type) ? $type : null);

        if (! $modelClass || ! class_exists($modelClass)) {
            throw new InvalidArgumentException("Invalid bookmarkable type: {$type}");
        }

        $model = $modelClass::find($id);
        if (! $model) {
            throw new InvalidArgumentException("Target model not found for {$type} #{$id}");
        }

        return $model;
    }

    /**
     * Explicitly save/bookmark a model.
     */
    public function bookmark(Model $model, User|int $user, string $collection = 'default'): array
    {
        $userId = $user instanceof User ? $user->id : $user;

        Bookmark::firstOrCreate([
            'user_id' => $userId,
            'bookmarkable_type' => $model->getMorphClass(),
            'bookmarkable_id' => $model->getKey(),
            'collection' => $collection,
        ]);

        $totalBookmarks = Bookmark::where('bookmarkable_type', $model->getMorphClass())
            ->where('bookmarkable_id', $model->getKey())
            ->where('collection', $collection)
            ->count();

        return [
            'bookmarked' => true,
            'collection' => $collection,
            'bookmarks_count' => $totalBookmarks,
        ];
    }

    /**
     * Explicitly remove bookmark of a model.
     */
    public function unbookmark(Model $model, User|int $user, string $collection = 'default'): array
    {
        $userId = $user instanceof User ? $user->id : $user;

        Bookmark::where('bookmarkable_type', $model->getMorphClass())
            ->where('bookmarkable_id', $model->getKey())
            ->where('user_id', $userId)
            ->where('collection', $collection)
            ->delete();

        $totalBookmarks = Bookmark::where('bookmarkable_type', $model->getMorphClass())
            ->where('bookmarkable_id', $model->getKey())
            ->where('collection', $collection)
            ->count();

        return [
            'bookmarked' => false,
            'collection' => $collection,
            'bookmarks_count' => $totalBookmarks,
        ];
    }

    /**
     * Toggle bookmark/save state for a model in a specified collection.
     */
    public function toggleBookmark(Model $model, User|int $user, string $collection = 'default'): array
    {
        $userId = $user instanceof User ? $user->id : $user;

        $existing = Bookmark::where('bookmarkable_type', $model->getMorphClass())
            ->where('bookmarkable_id', $model->getKey())
            ->where('user_id', $userId)
            ->where('collection', $collection)
            ->first();

        if ($existing) {
            return $this->unbookmark($model, $user, $collection);
        }

        return $this->bookmark($model, $user, $collection);
    }

    /**
     * Get paginated bookmarks of a user with optional collection and type filters.
     */
    public function getUserBookmarks(
        User|int $user,
        ?string $collection = null,
        ?string $type = null,
        int $perPage = 15,
        bool $cursor = false
    ): Paginator {
        $userId = $user instanceof User ? $user->id : $user;

        $query = Bookmark::where('user_id', $userId)
            ->with(['bookmarkable'])
            ->latest('id');

        if (! empty($collection)) {
            $query->where('collection', $collection);
        }

        if (! empty($type)) {
            $morphMap = Relation::morphMap();
            $morphType = $morphMap[$type] ?? $type;
            $query->where('bookmarkable_type', $morphType);
        }

        return $cursor ? $query->cursorPaginate($perPage) : $query->paginate($perPage);
    }

    /**
     * Get paginated list of users who bookmarked this model.
     */
    public function getBookmarkers(Model $model, string $collection = 'default', int $perPage = 20): LengthAwarePaginator
    {
        return Bookmark::where('bookmarkable_type', $model->getMorphClass())
            ->where('bookmarkable_id', $model->getKey())
            ->where('collection', $collection)
            ->with('user:id,name,avatar,email')
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Check if a model is bookmarked by a user in a specific collection.
     */
    public function isBookmarked(Model $model, User|int|null $user, string $collection = 'default'): bool
    {
        if (! $user) {
            return false;
        }

        $userId = $user instanceof User ? $user->id : $user;

        return Bookmark::where('bookmarkable_type', $model->getMorphClass())
            ->where('bookmarkable_id', $model->getKey())
            ->where('user_id', $userId)
            ->where('collection', $collection)
            ->exists();
    }

    /**
     * Get collection breakdown counts for a model.
     */
    public function getCollectionsSummary(Model $model): array
    {
        return Bookmark::where('bookmarkable_type', $model->getMorphClass())
            ->where('bookmarkable_id', $model->getKey())
            ->selectRaw('collection, count(*) as count')
            ->groupBy('collection')
            ->pluck('count', 'collection')
            ->toArray();
    }
}
