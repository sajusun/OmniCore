<?php

declare(strict_types=1);

namespace App\Modules\Review\Services;

use App\Models\User;
use App\Modules\Notification\Services\NotificationService;
use App\Modules\Order\Models\Order;
use App\Modules\Review\Enums\ReviewStatus;
use App\Modules\Review\Models\Review;
use App\Modules\Review\Models\ReviewMedia;
use App\Modules\Review\Models\ReviewVote;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReviewService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Submit or update a review on a reviewable entity.
     */
    public function submitReview(
        User $user,
        Model $reviewable,
        array $data,
        array $mediaFiles = []
    ): Review {
        return DB::transaction(function () use ($user, $reviewable, $data, $mediaFiles) {
            $isVerified = $this->checkVerifiedBuyer($user, $reviewable);

            $review = Review::updateOrCreate(
                [
                    'user_id'         => $user->id,
                    'reviewable_type' => get_class($reviewable),
                    'reviewable_id'   => $reviewable->id,
                ],
                [
                    'rating'            => (int) ($data['rating'] ?? 5),
                    'title'             => $data['title'] ?? null,
                    'comment'           => $data['comment'],
                    'criteria_ratings'  => $data['criteria_ratings'] ?? null,
                    'is_verified_buyer' => $isVerified,
                    'status'            => ReviewStatus::APPROVED->value,
                ]
            );

            // Save media files
            foreach ($mediaFiles as $index => $file) {
                if ($file instanceof UploadedFile) {
                    $this->saveMedia($review, $file, $index);
                }
            }

            // Notify owner or admins
            $admins = User::whereHas('roles', fn($q) => $q->whereIn('name', ['super_admin', 'admin', 'Super Admin', 'Admin']))->get();
            if ($admins->isNotEmpty()) {
                $this->notificationService->sendMany(
                    $admins,
                    title: "New Review ({$review->rating}★)",
                    body: "{$user->name} reviewed a " . class_basename($reviewable) . ": " . Str::limit($review->comment, 60),
                    type: 'review',
                    referenceType: 'review',
                    referenceId: $review->id,
                    meta: ['review_id' => $review->id, 'rating' => $review->rating]
                );
            }

            return $review->fresh(['media', 'user']);
        });
    }

    /**
     * Vote on review helpfulness.
     */
    public function voteReview(Review $review, User $user, bool $isHelpful): ReviewVote
    {
        return DB::transaction(function () use ($review, $user, $isHelpful) {
            $vote = ReviewVote::updateOrCreate(
                [
                    'review_id' => $review->id,
                    'user_id'   => $user->id,
                ],
                [
                    'is_helpful' => $isHelpful,
                ]
            );

            // Recalculate vote counts
            $helpfulCount = ReviewVote::where('review_id', $review->id)->where('is_helpful', true)->count();
            $unhelpfulCount = ReviewVote::where('review_id', $review->id)->where('is_helpful', false)->count();

            $review->update([
                'helpful_count'   => $helpfulCount,
                'unhelpful_count' => $unhelpfulCount,
            ]);

            return $vote;
        });
    }

    /**
     * Post a vendor/admin reply to a review.
     */
    public function replyToReview(Review $review, string $replyMessage): Review
    {
        $review->update([
            'vendor_reply'      => $replyMessage,
            'vendor_replied_at' => now(),
        ]);

        // Notify reviewer
        $this->notificationService->send(
            $review->user,
            title: "Response to your review",
            body: "The vendor replied: " . Str::limit($replyMessage, 80),
            type: 'review_reply',
            referenceType: 'review',
            referenceId: $review->id,
            meta: ['review_id' => $review->id]
        );

        return $review->fresh();
    }

    /**
     * Moderate review status.
     */
    public function moderateReview(Review $review, ReviewStatus|string $status): Review
    {
        $statusVal = $status instanceof ReviewStatus ? $status->value : $status;
        $review->update(['status' => $statusVal]);
        return $review->fresh();
    }

    /**
     * Calculate review breakdown summary for a model.
     */
    public function getSummary(Model $reviewable): array
    {
        $reviews = Review::where('reviewable_type', get_class($reviewable))
            ->where('reviewable_id', $reviewable->id)
            ->approved()
            ->get();

        $total = $reviews->count();
        $average = $total > 0 ? round((float) $reviews->avg('rating'), 1) : 0.0;

        $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($reviews as $rev) {
            $star = (int) $rev->rating;
            if (isset($distribution[$star])) {
                $distribution[$star]++;
            }
        }

        return [
            'average_rating'      => $average,
            'total_reviews'       => $total,
            'rating_distribution' => $distribution,
            'has_media_count'     => $reviews->filter(fn($r) => $r->media()->count() > 0)->count(),
        ];
    }

    /**
     * Check if the user purchased this item.
     */
    protected function checkVerifiedBuyer(User $user, Model $reviewable): bool
    {
        if (class_basename($reviewable) === 'Product' && class_exists(Order::class)) {
            return Order::where('user_id', $user->id)
                ->whereIn('status', ['completed', 'delivered', 'processing'])
                ->whereHas('items', function ($q) use ($reviewable) {
                    $q->where('product_id', $reviewable->id);
                })
                ->exists();
        }

        return false;
    }

    /**
     * Save an uploaded review media file.
     */
    protected function saveMedia(Review $review, UploadedFile $file, int $sortOrder = 0): ReviewMedia
    {
        $isImage = str_starts_with($file->getMimeType() ?: '', 'image');
        $type = $isImage ? 'image' : 'video';

        $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('reviews/media', $filename, 'public');

        return ReviewMedia::create([
            'review_id'  => $review->id,
            'file_path'  => $path,
            'file_type'  => $type,
            'file_size'  => $file->getSize() ?: 0,
            'sort_order' => $sortOrder,
        ]);
    }
}
