<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use App\Enums\PostType;
use App\Helpers\Helper;
use App\Models\PostLike;
use App\Models\SavedPost;
use Illuminate\Support\Str;
use App\Enums\PostStatusEnum;
use App\Enums\PostVisibilityEnum;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Modules\Media\Traits\HandlesMedia;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostService
{
    use HandlesMedia;
    private User $user;
    private Post $post;
    public function __construct()
    {
        $this->user = auth('api')->user();
    }

    public function index(): LengthAwarePaginator
    {
        return Post::query()
            ->with(['user', 'media', 'sharedPost.user', 'sharedPost.media',])
            ->withCount(['likes', 'comments', 'shares', 'views'])
            ->where('user_id', auth('api')->id())->latest()->paginate(15);
    }

    public function show(Post $post): Post
    {
        return $post->load([
            'user',
            'media',
            'sharedPost.user',
            'sharedPost.media',
            'comments.user',
            'comments.likes',
            'comments.replies.user',
            'comments.replies.likes',
            'visibleUsers',
        ])->loadCount([
            'likes',
            'comments',
            'shares',
            'views',
        ]);
    }

    public function store(array $data): Post
    {
        return DB::transaction(function () use ($data) {

            $thumbnail = null;

            if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
                $thumbnail = Helper::fileUpload($data['thumbnail'], 'post',);
            }

            $post = Post::create([
                'user_id'      => Auth::id(),
                'title'        => $data['title'] ?? null,
                'slug'         => Helper::makeSlug(Post::class, $data['title'] ?? Str::random()),
                'content'      => $data['content'],
                'thumbnail'    => $thumbnail,
                'visibility'   => $data['visibility'] ?? PostVisibilityEnum::PUBLIC->value,
                'type'         => $data['type'] ?? 'post',
                'status'       => 'published',
                'shared_post_id' => $data['shared_post_id'] ?? null,
            ]);

            if ($post->visibility === PostVisibilityEnum::FRIENDS->value && !empty($data['friend_ids'])) {
                $post->visibleUsers()->sync($data['friend_ids']);
            }

            if (! empty($data['media'])) {

                foreach ($data['media'] as $key => $media) {
                    $this->uploadMedia($post, $media);
                }
            }

            return $post->load(['user', 'media',]);
        });
    }

    public function update(Post $post, array $data): Post
    {
        return DB::transaction(function () use ($post, $data) {

            if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {

                if (!empty($post->thumbnail)) {
                    Helper::fileDelete($post->thumbnail);
                }

                $post->thumbnail = Helper::fileUpload($data['thumbnail'], 'post');
            }

            $post->update([
                'title'          => $data['title'] ?? $post->title,
                'content'        => $data['content'],
                'visibility'     => $data['visibility'] ?? $post->visibility,
                'type'           => $data['type'] ?? $post->type,
                'shared_post_id' => $data['shared_post_id'] ?? $post->shared_post_id,
            ]);

            if ($post->visibility === PostVisibilityEnum::FRIENDS->value && !empty($data['friend_ids'])) {
                $post->visibleUsers()->sync($data['friend_ids']);
            }

            if (!empty($data['media'])) {

                foreach ($data['media'] as $media) {

                    $this->updateMedia($post, $media);
                }
            }

            return $post->load(['user', 'media',]);
        });
    }

    public function destroy(Post $post): bool
    {
        return DB::transaction(function () use ($post) {

            // Delete Thumbnail
            if (!empty($post->thumbnail)) {
                Helper::fileDelete($post->thumbnail);
            }

            // Delete All Media
            $mediaIds = $post->media()->pluck('id')->toArray();

            if (!empty($mediaIds)) {
                $this->deleteMedia($mediaIds);
            }

            // Soft Delete Post
            return $post->delete();
        });
    }

    public function feed(): LengthAwarePaginator
    {
        $authId = Auth::id();

        return Post::query()
            ->with([
                'user',
                'media',
                'visibleUsers',
                'sharedPost.user',
                'sharedPost.media',
            ])
            ->withCount([
                'likes',
                'comments',
                'shares',
                'views',
            ])->where('status', PostStatusEnum::PUBLISHED->value)
            ->where(function ($query) use ($authId) {

                $query->where('user_id', $authId);
                $query->orWhere('visibility', PostVisibilityEnum::PUBLIC->value);

                $query->orWhere(function ($q) use ($authId) {
                    $q->where('visibility', PostVisibilityEnum::FOLLOWERS->value)
                        ->whereHas('user.followers', function ($follow) use ($authId) {
                            $follow->where('follower_id', $authId);
                        });
                });

                $query->orWhere(function ($q) use ($authId) {
                    $q->where('visibility', PostVisibilityEnum::FRIENDS->value)
                        ->whereHas('visibleUsers', function ($friend) use ($authId) {
                            $friend->where('users.id', $authId);
                        });
                });

                $query->orWhere(function ($q) use ($authId) {
                    $q->where('visibility', PostVisibilityEnum::PRIVATE->value)->where('user_id', $authId);
                });
            })->latest()->latest('posts.created_at')->paginate(15);
    }

    public function toggleLike(Post $post): bool
    {
        $like = PostLike::where('post_id', $post->id)->where('user_id', auth()->id())->first();

        if ($like) {
            $like->delete();
            return false;
        }

        PostLike::create([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
            'reaction' => 'like',
        ]);

        return true;
    }

    public function share(Post $post, array $data): Post
    {
        return DB::transaction(function () use ($post, $data) {

            $originalPostId = $post->type === PostType::SHARED ? $post->shared_post_id : $post->id;

            $sharedPost = Post::create([
                'user_id'        => auth()->id(),
                'shared_post_id' => $originalPostId,
                'title'          => null,
                'slug'           => null,
                'content'        => $data['content'] ?? null,
                'thumbnail'      => null,
                'visibility'     => $data['visibility'] ?? PostVisibilityEnum::PUBLIC->value,
                'type'           => PostType::SHARED,
                'status'         => $post->status,
            ]);

            return $sharedPost->load(['user', 'sharedPost.user', 'sharedPost.media',]);
        });
    }


    public function toggleSave(Post $post): bool
    {
        $saved = SavedPost::where('user_id', auth()->id())->where('post_id', $post->id)->first();

        if ($saved) {
            $saved->delete();
            return false;
        }

        SavedPost::create([
            'user_id' => auth()->id(),
            'post_id' => $post->id,
        ]);

        return true;
    }

    public function savedPosts()
    {
        $user = auth()->user();

        $data = $user->savedPosts()->with(['user', 'media', 'media'])->latest()->paginate(15);
        return $data;
    }
}
