<?php

namespace App\Modules\Interaction\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Modules\Interaction\Http\Requests\ToggleBookmarkRequest;
use App\Modules\Interaction\Http\Resources\BookmarkResource;
use App\Modules\Interaction\Services\BookmarkService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookmarkApiController extends Controller
{
    public function __construct(
        protected BookmarkService $bookmarkService
    ) {}

    /**
     * Toggle bookmark/save/wishlist state for any model.
     */
    public function toggle(ToggleBookmarkRequest $request): JsonResponse
    {
        try {
            $model = $this->bookmarkService->resolveModel(
                $request->getTargetType(),
                $request->getTargetId()
            );

            $collection = $request->getCollection();

            $result = $this->bookmarkService->toggleBookmark(
                $model,
                $request->user(),
                $collection
            );

            $message = $result['bookmarked']
                ? ($collection === 'wishlist' ? 'Added to wishlist.' : 'Saved to bookmarks.')
                : ($collection === 'wishlist' ? 'Removed from wishlist.' : 'Removed from bookmarks.');

            return Helper::jsonResponse(true, $message, 200, $result);
        } catch (Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get paginated bookmarks of the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'collection' => ['nullable', 'string', 'max:64'],
            'type' => ['nullable', 'string', 'max:64'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'cursor' => ['nullable', 'string'],
        ]);

        try {
            $isCursor = $request->has('cursor');
            $bookmarks = $this->bookmarkService->getUserBookmarks(
                $request->user(),
                $request->input('collection'),
                $request->input('type'),
                (int) $request->input('per_page', 15),
                $isCursor
            );

            return Helper::jsonResponse(
                true,
                'Bookmarks retrieved successfully.',
                200,
                BookmarkResource::collection($bookmarks),
                true,
                $bookmarks
            );
        } catch (Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 400);
        }
    }
}
