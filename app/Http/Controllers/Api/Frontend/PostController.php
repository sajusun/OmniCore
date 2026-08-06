<?php

namespace App\Http\Controllers\Api\Frontend;

use Exception;
use App\Models\Post;
use App\Models\Image;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;

class PostController extends Controller
{

    public function __construct(
        protected PostService $postService
    ) {}

    public function feed(): JsonResponse
    {
        $posts = $this->postService->feed();
        return Helper::jsonResponse(true, 'Feed fetched successfully.', 200, PostResource::collection($posts));
    }

    public function index(): JsonResponse
    {
        $posts = $this->postService->index();
        return Helper::jsonResponse(true, 'Posts fetched successfully.', 200, PostResource::collection($posts));
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $post = $this->postService->store($request->validated());

        return Helper::jsonResponse(
            true,
            'Post created successfully.',
            201,
            new PostResource($post)
        );
    }

    public function show(Post $post): JsonResponse
    {
        $post = $this->postService->show($post);
        return Helper::jsonResponse(true, 'Post fetched successfully.', 200, new PostResource($post));
    }

    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $post = $this->postService->update(
            $post,
            $request->validated()
        );
        return Helper::jsonResponse(true, 'Post updated successfully.', 200, new PostResource($post));
    }

    public function destroy(Post $post): JsonResponse
    {
        $this->postService->destroy($post);
        return Helper::jsonResponse(true, 'Post deleted successfully.', 200);
    }


    public function like(Post $post): JsonResponse
    {
        $liked = $this->postService->toggleLike($post);
        return Helper::jsonResponse(true, $liked ? 'Post liked successfully.' : 'Post unliked successfully.', 200);
    }
    public function likedUser(Post $post): JsonResponse
    {
        $likedUsers = $this->postService->LikedUsers($post);

        return Helper::jsonResponse(true, 'Liked users fetched successfully.', 200, UserResource::collection($likedUsers));
    }

    public function repost(Request $request, Post $post): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'content' => ['nullable', 'string'],
            'visibility' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return Helper::jsonResponse(false, 'Validation failed.', 422, $validator->errors());
        }

        $sharedPost = $this->postService->share($post, $validator->validated());

        return Helper::jsonResponse(true, 'Post shared successfully.', 201, new PostResource($sharedPost));
    }

    public function share(string $share_link): JsonResponse
    {
        $post = Post::where('share_link', $share_link)->first();

        if (!$post) {
            return Helper::jsonResponse(false, 'Failed to get post.', 422);
        }

        return $this->show($post);
    }

    public function toggleSave(Post $post): JsonResponse
    {
        $saved = $this->postService->toggleSave($post);
        return Helper::jsonResponse(true, $saved ? 'Post saved successfully.' : 'Post removed from saved posts.', 200);
    }
    public function savedPosts(): JsonResponse
    {
        $posts = $this->postService->savedPosts();
        return Helper::jsonResponse(true, 'Saved posts fetched successfully.', 200, PostResource::collection($posts));
    }
}
