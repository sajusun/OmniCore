<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Models\Post;
use App\Helpers\Helper;
use App\Models\PostComment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\PostCommentService;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PostCommentResource;

class PostCommentController extends Controller
{
    public function __construct(
        protected PostCommentService $postCommentService
    ) {}

    public function index(Post $post): JsonResponse
    {
        $comments = $this->postCommentService->index($post);

        return Helper::jsonResponse(true, 'Comments fetched successfully.', 200, PostCommentResource::collection($comments));
    }

    public function store(Request $request, Post $post): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'comment' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return Helper::jsonResponse(false, 'Validation failed.', 422, $validator->errors());
        }

        $comment = $this->postCommentService->store(
            $post,
            $validator->validated()
        );

        return Helper::jsonResponse(true, 'Comment added successfully.', 201, new PostCommentResource($comment));
    }

    public function update(Request $request, PostComment $comment): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'comment' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return Helper::jsonResponse(
                false,
                'Validation failed.',
                422,
                $validator->errors()
            );
        }

        $comment = $this->postCommentService->update(
            $comment,
            $validator->validated()
        );

        return Helper::jsonResponse(true, 'Comment updated successfully.', 200, new PostCommentResource($comment));
    }

    public function destroy(PostComment $comment): JsonResponse
    {
        $this->postCommentService->destroy($comment);

        return Helper::jsonResponse(true, 'Comment deleted successfully.', 200);
    }

    public function reply(Request $request, PostComment $comment): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'comment' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return Helper::jsonResponse(false, 'Validation failed.', 422, $validator->errors());
        }

        $reply = $this->postCommentService->reply(
            $comment,
            $validator->validated()
        );

        return Helper::jsonResponse(true, 'Reply added successfully.', 201, new PostCommentResource($reply));
    }

    public function toggleLike(PostComment $comment): JsonResponse
    {
        $liked = $this->postCommentService->toggleLike($comment);

        return Helper::jsonResponse(
            true,
            $liked ? 'Comment liked successfully.' : 'Comment unliked successfully.',
            200
        );
    }

    public function replies(PostComment $comment): JsonResponse
    {
        $replies = $this->postCommentService->replies($comment);

        return Helper::jsonResponse(true, 'Replies fetched successfully.', 200, PostCommentResource::collection($replies));
    }
}
