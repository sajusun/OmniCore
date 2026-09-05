<?php

namespace App\Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Product\Http\Requests\StoreReviewRequest;
use App\Modules\Product\Http\Resources\ReviewResource;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(protected ReviewService $reviewService)
    {
        parent::__construct();
    }

    /**
     * Get approved reviews for a product
     */
    public function index(int $productId, Request $request): JsonResponse
    {
        $product = Product::findOrFail($productId);
        $reviews = $this->reviewService->getProductReviews($product, (int) $request->get('per_page', 10));

        return $this->paginated(
            $reviews,
            ReviewResource::class,
            'Product reviews fetched successfully.'
        );
    }

    /**
     * Store or update product review with photos
     */
    public function store(int $productId, StoreReviewRequest $request): JsonResponse
    {
        $product = Product::findOrFail($productId);
        $user = $request->user();

        $photos = $request->file('photos', []);
        $review = $this->reviewService->createReview($user, $product, $request->validated(), $photos);

        return $this->created(
            new ReviewResource($review),
            'Your review has been submitted successfully.'
        );
    }
}
