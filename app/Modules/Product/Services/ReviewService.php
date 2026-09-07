<?php

namespace App\Modules\Product\Services;

use App\Models\User;
use App\Modules\Media\Services\MediaService;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductReview;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReviewService
{
    public function __construct(protected MediaService $mediaService) {}

    /**
     * Get approved reviews for a product with pagination
     */
    public function getProductReviews(Product $product, int $perPage = 15): LengthAwarePaginator
    {
        return $product->reviews()
            ->with(['user:id,name,avatar', 'media'])
            ->where('status', 'approved')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create review with rating and optional photos
     */
    public function createReview(User $user, Product $product, array $data, array $photos = []): ProductReview
    {
        // Check if user already reviewed this product
        $review = ProductReview::updateOrCreate(
            [
                'product_id' => $product->id,
                'user_id' => $user->id,
            ],
            [
                'rating' => (int) $data['rating'],
                'title' => $data['title'] ?? null,
                'comment' => $data['comment'] ?? null,
                'status' => 'approved', // default auto-approve or can be set to pending based on settings
                'is_verified_purchase' => $data['is_verified_purchase'] ?? false,
            ]
        );

        if (! empty($photos)) {
            foreach ($photos as $photo) {
                $this->mediaService->upload($photo, $review, 'photos', 'reviews');
            }
        }

        // Recalculate stats on product
        $product->updateRatingStats();

        return $review->load(['user:id,name,avatar', 'media']);
    }

    /**
     * Moderate review status (approve/reject)
     */
    public function updateStatus(ProductReview $review, string $status): ProductReview
    {
        $review->update(['status' => $status]);
        $review->product->updateRatingStats();

        return $review;
    }
}
