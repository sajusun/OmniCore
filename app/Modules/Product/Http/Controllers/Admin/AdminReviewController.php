<?php

namespace App\Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Product\Http\Resources\ReviewResource;
use App\Modules\Product\Models\ProductReview;
use App\Modules\Product\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function __construct(protected ReviewService $reviewService) {}

    /**
     * Moderation list of reviews
     */
    public function index(Request $request): JsonResponse
    {
        $query = ProductReview::with(['product:id,name,slug', 'user:id,name,avatar', 'media']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $reviews = $query->latest()->paginate((int) $request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => ReviewResource::collection($reviews),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }

    /**
     * Moderate review status (approve, reject, pending)
     */
    public function updateStatus(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $review = ProductReview::findOrFail($id);
        $this->reviewService->updateStatus($review, $request->status);

        return response()->json([
            'success' => true,
            'message' => "Review status updated to {$request->status}.",
            'data' => new ReviewResource($review->fresh(['user', 'media'])),
        ]);
    }

    /**
     * Delete review
     */
    public function destroy(int $id): JsonResponse
    {
        $review = ProductReview::findOrFail($id);
        $product = $review->product;
        $review->delete();

        if ($product) {
            $product->updateRatingStats();
        }

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully.',
        ]);
    }
}
