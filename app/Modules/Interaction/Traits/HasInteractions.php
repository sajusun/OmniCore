<?php

namespace App\Modules\Interaction\Traits;

/**
 * Unified Trait bundling Comments, Likes, Share Links, Views, and Bookmarks for any model.
 */
trait HasInteractions
{
    use HasComments, HasLikes, HasShareLinks, HasViews, HasBookmarks;
}
