<?php

declare(strict_types=1);

namespace App\Modules\Review\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Post\Models\Post;
use App\Modules\Product\Models\Product;
use App\Modules\Review\Models\Review;
use App\Modules\Review\Resources\ReviewResource;
use App\Modules\Review\Resources\ReviewSummaryResource;
use App\Modules\Review\Services\ReviewService;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewApiController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected ReviewService $reviewService
    ) {}

    /**
     * List reviews for a specific reviewable item.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'reviewable_type' => 'required|string',
            'reviewable_id'   => 'required|integer',
        ]);

        $modelClass = $this->resolveModelClass($request->reviewable_type);
        if (!$modelClass) {
            return $this->error('Invalid reviewable type.', 422);
        }

        $query = Review::where('reviewable_type', $modelClass)
            ->where('reviewable_id', $request->reviewable_id)
            ->approved()
            ->with(['user', 'media', 'votes']);

        // Star filter
        if ($request->filled('rating')) {
            $query->where('rating', $request->integer('rating'));
        }

        // Verified buyer only
        if ($request->boolean('verified_only')) {
            $query->where('is_verified_buyer', true);
        }

        // Has media
        if ($request->boolean('has_media')) {
            $query->has('media');
        }

        // Sorting
        $sort = $request->get('sort', 'recent');
        match ($sort) {
            'helpful' => $query->orderBy('helpful_count', 'desc')->orderBy('created_at', 'desc'),
            'highest' => $query->orderBy('rating', 'desc')->orderBy('created_at', 'desc'),
            'lowest'  => $query->orderBy('rating', 'asc')->orderBy('created_at', 'desc'),
            default   => $query->orderBy('created_at', 'desc'),
        };

        $reviews = $query->paginate($request->integer('per_page', 10));

        return $this->paginated(
            $reviews,
            ReviewResource::class,
            'Reviews retrieved successfully.'
        );
    }

    /**
     * Get review summary breakdown for an item.
     */
    public function summary(Request $request): JsonResponse
    {
        $request->validate([
            'reviewable_type' => 'required|string',
            'reviewable_id'   => 'required|integer',
        ]);

        $modelClass = $this->resolveModelClass($request->reviewable_type);
        if (!$modelClass) {
            return $this->error('Invalid reviewable type.', 422);
        }

        $item = $modelClass::find($request->reviewable_id);
        if (!$item) {
            return $this->notFound('Target item not found.');
        }

        $summary = $this->reviewService->getSummary($item);

        return $this->success(
            new ReviewSummaryResource($summary),
            'Review summary retrieved successfully.'
        );
    }

    /**
     * Create or update a review.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reviewable_type'  => 'required|string',
            'reviewable_id'    => 'required|integer',
            'rating'           => 'required|integer|min:1|max:5',
            'title'            => 'nullable|string|max:255',
            'comment'          => 'required|string|min:5',
            'criteria_ratings' => 'nullable|array',
            'media'            => 'nullable|array',
            'media.*'          => 'file|max:10240|mimes:jpeg,png,jpg,gif,mp4,mov,avi,webp',
        ]);

        $modelClass = $this->resolveModelClass($validated['reviewable_type']);
        if (!$modelClass) {
            return $this->error('Invalid reviewable type.', 422);
        }

        $item = $modelClass::find($validated['reviewable_id']);
        if (!$item) {
            return $this->notFound('Target item not found.');
        }

        $review = $this->reviewService->submitReview(
            $request->user(),
            $item,
            $validated,
            $request->file('media', [])
        );

        return $this->created(
            new ReviewResource($review),
            'Review submitted successfully.'
        );
    }

    /**
     * Vote on review helpfulness.
     */
    public function vote(Request $request, Review $review): JsonResponse
    {
        $validated = $request->validate([
            'is_helpful' => 'required|boolean',
        ]);

        $this->reviewService->voteReview(
            $review,
            $request->user(),
            (bool) $validated['is_helpful']
        );

        return $this->success(
            new ReviewResource($review->fresh(['user', 'media', 'votes'])),
            'Feedback recorded successfully.'
        );
    }

    /**
     * Delete user's own review.
     */
    public function destroy(Request $request, Review $review): JsonResponse
    {
        if ($review->user_id !== $request->user()->id && !$request->user()->hasAnyRole(['super_admin', 'admin', 'Super Admin', 'Admin'])) {
            return $this->forbidden('Unauthorized to delete this review.');
        }

        $review->delete();

        return $this->success(null, 'Review deleted successfully.');
    }

    /**
     * Map string alias to Model class.
     */
    protected function resolveModelClass(string $type): ?string
    {
        $map = [
            'product' => Product::class,
            'post'    => Post::class,
            'user'    => \App\Models\User::class,
        ];

        if (isset($map[strtolower($type)])) {
            return $map[strtolower($type)];
        }

        if (class_exists($type)) {
            return $type;
        }

        return null;
    }
}
