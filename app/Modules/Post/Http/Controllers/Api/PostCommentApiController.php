<?php

namespace App\Modules\Post\Http\Controllers\Api;

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
    ) {
        parent::__construct();
    }

    public function index(Post $post): JsonResponse
    {
        $comments = $this->postCommentService->index($post);
        return $this->paginated($comments, PostCommentResource::class, 'Comments fetched successfully.');
    }

    public function store(StoreCommentRequest $request, Post $post): JsonResponse
    {
        $comment = $this->postCommentService->store(
            $post,
            $request->validated()
        );

        return $this->created(new PostCommentResource($comment), 'Comment added successfully.');
    }

    public function update(StoreCommentRequest $request, Comment $comment): JsonResponse
    {
        $comment = $this->postCommentService->update(
            $comment,
            $request->validated()
        );

        return $this->success(new PostCommentResource($comment), 'Comment updated successfully.');
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $this->postCommentService->destroy($comment);
        return $this->success(null, 'Comment deleted successfully.');
    }

    public function reply(StoreCommentRequest $request, Comment $comment): JsonResponse
    {
        $reply = $this->postCommentService->reply(
            $comment,
            $request->validated()
        );

        return $this->created(new PostCommentResource($reply), 'Reply added successfully.');
    }

    public function toggleLike(Comment $comment): JsonResponse
    {
        $liked = $this->postCommentService->toggleLike($comment);

        return $this->success(
            ['liked' => $liked],
            $liked ? 'Comment liked successfully.' : 'Comment unliked successfully.'
        );
    }

    public function replies(Comment $comment): JsonResponse
    {
        $replies = $this->postCommentService->replies($comment);
        return $this->paginated($replies, PostCommentResource::class, 'Replies fetched successfully.');
    }
}
