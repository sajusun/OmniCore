<?php

namespace App\Modules\Interaction\Traits;

use App\Models\User;

/**
 * Unified Trait bundling Comments, Likes, Share Links, Views, and Bookmarks for any model.
 */
trait HasInteractions
{
    use HasBookmarks, HasComments, HasLikes, HasShareLinks, HasViews;

    /**
     * Get complete interaction statistics in one unified array.
     * Ideal for admin show/details views, API resource transformers, or dashboards.
     */
    public function interactionStats(): array
    {
        return [
            'likes_count' => $this->likesCount(),
            'comments_count' => $this->commentsCount(),
            'total_comments_count' => $this->totalCommentsCount(),
            'views_count' => $this->viewsCount(),
            'unique_views_count' => $this->uniqueViewsCount(),
            'bookmarks_count' => $this->bookmarksCount(),
            'shares_count' => $this->sharesCount(),
            'share_clicks_count' => $this->totalShareClicksCount(),
        ];
    }

    /**
     * Get interaction status of a specific user with this model.
     */
    public function userInteractions(User|int|null $user): array
    {
        if (! $user) {
            return [
                'is_liked' => false,
                'is_bookmarked' => false,
                'is_viewed' => false,
            ];
        }

        return [
            'is_liked' => $this->isLikedBy($user),
            'is_bookmarked' => $this->isBookmarkedBy($user),
            'is_viewed' => $this->isViewedBy($user),
        ];
    }
}
