<?php

namespace App\Modules\Post\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Modules\Post\Http\Requests\StorePostRequest;
use App\Modules\Post\Http\Requests\UpdatePostRequest;
use App\Modules\Post\Http\Resources\PostResource;
use App\Modules\Post\Models\Post;
use App\Modules\Post\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostApiController extends Controller
{
    public function __construct(
        protected PostService $postService
    ) {
        parent::__construct();
    }

    public function feed(): JsonResponse
    {
        $posts = $this->postService->feed();
        return $this->paginated($posts, PostResource::class, 'Feed fetched successfully.');
    }

    public function index(): JsonResponse
    {
        $posts = $this->postService->index();
        return $this->paginated($posts, PostResource::class, 'Posts fetched successfully.');
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $post = $this->postService->store($request->validated());

        return $this->created(
            new PostResource($post),
            'Post created successfully.'
        );
    }

    public function show(Post $post): JsonResponse
    {
        $post = $this->postService->show($post);
        return $this->success(new PostResource($post), 'Post fetched successfully.');
    }

    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $post = $this->postService->update(
            $post,
            $request->validated()
        );
        return $this->success(new PostResource($post), 'Post updated successfully.');
    }

    public function destroy(Post $post): JsonResponse
    {
        $this->postService->destroy($post);
        return $this->success(null, 'Post deleted successfully.');
    }

    public function like(Post $post): JsonResponse
    {
        $liked = $this->postService->toggleLike($post);
        return $this->success(['liked' => $liked], $liked ? 'Post liked successfully.' : 'Post unliked successfully.');
    }

    public function likedUser(Post $post): JsonResponse
    {
        $likedUsers = $this->postService->likedUsers($post);

        return $this->paginated($likedUsers, UserResource::class, 'Liked users fetched successfully.');
    }

    public function repost(Request $request, Post $post): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'content'    => ['nullable', 'string'],
            'visibility' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $sharedPost = $this->postService->share($post, $validator->validated());

        return $this->created(new PostResource($sharedPost), 'Post shared successfully.');
    }

    public function share(string $share_link): JsonResponse
    {
        $post = Post::where('share_link', $share_link)->first();

        if (!$post) {
            return $this->notFound('Failed to get post.');
        }

        return $this->show($post);
    }

    public function toggleSave(Post $post): JsonResponse
    {
        $saved = $this->postService->toggleSave($post);
        return $this->success(['saved' => $saved], $saved ? 'Post saved successfully.' : 'Post removed from saved posts.');
    }

    public function savedPosts(): JsonResponse
    {
        $posts = $this->postService->savedPosts();
        return $this->paginated($posts, PostResource::class, 'Saved posts fetched successfully.');
    }
}
