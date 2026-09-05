<?php

namespace App\Modules\Interaction\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Interaction\Http\Requests\StoreCommentRequest;
use App\Modules\Interaction\Http\Resources\CommentResource;
use App\Modules\Interaction\Models\Comment;
use App\Modules\Interaction\Services\CommentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentApiController extends Controller
{
    public function __construct(
        protected CommentService $commentService
    ) {}

    /**
     * Get paginated comments for any model.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'subject_type' => ['required', 'string'],
            'subject_id' => ['required'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        try {
            $model = $this->commentService->resolveModel(
                $request->input('subject_type'),
                $request->input('subject_id')
            );

            $comments = $this->commentService->getCommentsForModel(
                $model,
                (int) $request->input('per_page', 15)
            );

            return $this->paginated($comments, CommentResource::class, 'Comments retrieved successfully.');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), null, 400);
        }
    }

    /**
     * Add a top-level comment or a reply.
     */
    public function store(StoreCommentRequest $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($request->filled('parent_id')) {
                $parentComment = Comment::findOrFail($request->input('parent_id'));
                $comment = $this->commentService->createReply(
                    $parentComment,
                    $user,
                    $request->input('body')
                );
            } else {
                $model = $this->commentService->resolveModel(
                    $request->input('subject_type'),
                    $request->input('subject_id')
                );
                $comment = $this->commentService->createComment(
                    $model,
                    $user,
                    $request->input('body')
                );
            }

            return $this->success(
                new CommentResource($comment->load('user')),
                'Comment added successfully.',
                201
            );
        } catch (Exception $e) {
            return $this->error($e->getMessage(), null, 400);
        }
    }

    /**
     * Delete a comment.
     */
    public function destroy(Comment $comment, Request $request): JsonResponse
    {
        try {
            $this->commentService->deleteComment($comment, $request->user());

            return $this->success(null, 'Comment deleted successfully.');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), null, 403);
        }
    }
}
