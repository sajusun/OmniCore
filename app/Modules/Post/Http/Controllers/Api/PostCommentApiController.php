<?php

namespace App\Modules\Post\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Modules\Interaction\Models\Comment;
use App\Modules\Post\Http\Requests\StoreCommentRequest;
use App\Modules\Post\Http\Resources\PostCommentResource;
use App\Modules\Post\Models\Post;
use App\Modules\Post\Services\PostCommentService;
use Illuminate\Http\JsonResponse;

class PostCommentApiController extends Controller
{
    public function __construct(
        protected PostCommentService $postCommentService
    ) {}

    public function index(Post $post): JsonResponse
    {
        $comments = $this->postCommentService->index($post);
        return Helper::jsonResponse(true, 'Comments fetched successfully.', 200, PostCommentResource::collection($comments));
    }

    public function store(StoreCommentRequest $request, Post $post): JsonResponse
    {
        $comment = $this->postCommentService->store(
            $post,
            $request->validated()
        );

        return Helper::jsonResponse(true, 'Comment added successfully.', 201, new PostCommentResource($comment));
    }

    public function update(StoreCommentRequest $request, Comment $comment): JsonResponse
    {
        $comment = $this->postCommentService->update(
            $comment,
            $request->validated()
        );

        return Helper::jsonResponse(true, 'Comment updated successfully.', 200, new PostCommentResource($comment));
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $this->postCommentService->destroy($comment);
        return Helper::jsonResponse(true, 'Comment deleted successfully.', 200);
    }

    public function reply(StoreCommentRequest $request, Comment $comment): JsonResponse
    {
        $reply = $this->postCommentService->reply(
            $comment,
            $request->validated()
        );

        return Helper::jsonResponse(true, 'Reply added successfully.', 201, new PostCommentResource($reply));
    }

    public function toggleLike(Comment $comment): JsonResponse
    {
        $liked = $this->postCommentService->toggleLike($comment);

        return Helper::jsonResponse(
            true,
            $liked ? 'Comment liked successfully.' : 'Comment unliked successfully.',
            200
        );
    }

    public function replies(Comment $comment): JsonResponse
    {
        $replies = $this->postCommentService->replies($comment);
        return Helper::jsonResponse(true, 'Replies fetched successfully.', 200, PostCommentResource::collection($replies));
    }
}
