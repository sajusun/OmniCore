<?php

namespace App\Modules\Interaction\Traits;

/**
 * Unified Trait bundling Comments, Likes, and Share Links for any model.
 */
trait HasInteractions
{
    use HasComments, HasLikes, HasShareLinks;
}
