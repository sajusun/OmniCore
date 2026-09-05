<?php

namespace App\Modules\Interaction\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Interaction\Http\Requests\ToggleLikeRequest;
use App\Modules\Interaction\Http\Resources\LikeResource;
use App\Modules\Interaction\Services\LikeService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeApiController extends Controller
{
    public function __construct(
        protected LikeService $likeService
    ) {}

    /**
     * Toggle like state on any model or comment.
     */
    public function toggle(ToggleLikeRequest $request): JsonResponse
    {
        try {
            $model = $this->likeService->resolveModel(
                $request->input('subject_type'),
                $request->input('subject_id')
            );

            $result = $this->likeService->toggleLike(
                $model,
                $request->user(),
                $request->input('type', 'like')
            );

            $message = $result['liked'] ? 'Liked successfully.' : 'Unliked successfully.';

            return $this->success($result, $message);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), null, 400);
        }
    }

    /**
     * Get paginated list of likers for a model.
     */
    public function likers(Request $request): JsonResponse
    {
        $request->validate([
            'subject_type' => ['required', 'string'],
            'subject_id' => ['required'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        try {
            $model = $this->likeService->resolveModel(
                $request->input('subject_type'),
                $request->input('subject_id')
            );

            $likers = $this->likeService->getLikers(
                $model,
                (int) $request->input('per_page', 20)
            );

            return $this->paginated($likers, LikeResource::class, 'Likers retrieved successfully.');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), null, 400);
        }
    }
}
