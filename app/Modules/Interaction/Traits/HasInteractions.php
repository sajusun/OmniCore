<?php

namespace App\Modules\Interaction\Traits;

/**
 * Unified Trait bundling Comments, Likes, Share Links, and Views for any model.
 */
trait HasInteractions
{
    use HasComments, HasLikes, HasShareLinks, HasViews;
}
