<?php

declare(strict_types=1);

namespace App\Modules\Review\Traits;

use App\Modules\Review\Models\Review;
use App\Modules\Review\Models\ReviewVote;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CanReview
{
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    public function reviewVotes(): HasMany
    {
        return $this->hasMany(ReviewVote::class, 'user_id');
    }
}
