<?php

declare(strict_types=1);

namespace App\Modules\Review\Events;

use App\Modules\Review\Models\Review;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReviewRepliedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Review $review,
        public readonly string $replyMessage
    ) {}
}
