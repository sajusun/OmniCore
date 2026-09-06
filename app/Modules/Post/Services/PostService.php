<?php

namespace App\Modules\Post\Services;

use App\Modules\Post\Enums\PostStatusEnum;
use App\Modules\Post\Enums\PostType;
use App\Modules\Post\Enums\PostVisibilityEnum;
use App\Helpers\Helper;
use App\Models\User;
use App\Modules\Media\Traits\HandlesMedia;
use App\Modules\Post\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostService
{
    use HandlesMedia;

    public function index(): LengthAwarePaginator
    {
        return Post::query()
            ->with(['user', 'media', 'sharedPost.user', 'sharedPost.media'])
            ->withCount(['likes', 'comments', 'shares', 'views'])
            ->where('user_id', auth('api')->id())
            ->latest()
            ->paginate(15);
    }

    public function getPostsForAdmin(array $filters = [])
    {
        $userId = $filters['user_id'] ?? $filters['user'] ?? null;

        return Post::query()
            ->with(['user', 'media', 'sharedPost.user', 'sharedPost.media'])
            ->withCount(['likes', 'comments', 'shares', 'views'])
            ->filterByUser($userId)
            ->when(!empty($filters['status']), function ($q) use ($filters) {
                return $q->where('status', $filters['status']);
            });
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
                $thumbnail = Helper::fileUpload($data['thumbnail'], 'post');
            }

            $post = Post::create([
                'user_id' => Auth::id() ?? auth('api')->id(),
                'title' => $data['title'] ?? null,
                'slug' => Helper::makeSlug(Post::class, $data['title'] ?? Str::random()),
                'content' => $data['content'] ?? null,
                'thumbnail' => $thumbnail,
                'visibility' => $data['visibility'] ?? PostVisibilityEnum::PUBLIC->value,
                'type' => $data['type'] ?? 'post',
                'status' => 'published',
                'shared_post_id' => $data['shared_post_id'] ?? null,
            ]);

            if ($post->visibility === PostVisibilityEnum::FRIENDS->value && !empty($data['friend_ids'])) {
                $post->visibleUsers()->sync($data['friend_ids']);
            }

            if (!empty($data['media'])) {
                foreach ($data['media'] as $media) {
                    $this->uploadMedia($post, $media);
                }
            }

            return $post->load(['user', 'media']);
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
                'title' => $data['title'] ?? $post->title,
                'content' => $data['content'] ?? $post->content,
                'visibility' => $data['visibility'] ?? $post->visibility,
                'type' => $data['type'] ?? $post->type,
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

            return $post->load(['user', 'media']);
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

            // Delete Post
            return (bool) $post->delete();
        });
    }

    public function feed(): LengthAwarePaginator
    {
        $authId = Auth::id() ?? auth('api')->id();

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
            ])
            ->where('status', PostStatusEnum::PUBLISHED->value)
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
            })
            ->latest('created_at')
            ->paginate(15);
    }

    public function toggleLike(Post $post): bool
    {
        $userId = Auth::id() ?? auth('api')->id();
        $res = $post->toggleLike($userId);

        return (bool) ($res['liked'] ?? false);
    }

    public function likedUsers(Post|int $post)
    {
        if (is_int($post)) {
            $post = Post::findOrFail($post);
        }

        return $post->likedUsers()->get();
    }

    public function share(Post $post, array $data): Post
    {
        return DB::transaction(function () use ($post, $data) {
            $originalPostId = $post->type === PostType::SHARED ? $post->shared_post_id : $post->id;
            $userId = Auth::id() ?? auth('api')->id();

            $sharedPost = Post::create([
                'user_id' => $userId,
                'shared_post_id' => $originalPostId,
                'title' => null,
                'slug' => null,
                'content' => $data['content'] ?? null,
                'thumbnail' => null,
                'visibility' => $data['visibility'] ?? PostVisibilityEnum::PUBLIC->value,
                'type' => PostType::SHARED,
                'status' => $post->status,
            ]);

            return $sharedPost->load(['user', 'sharedPost.user', 'sharedPost.media']);
        });
    }

    public function toggleSave(Post $post): bool
    {
        $userId = Auth::id() ?? auth('api')->id();
        $res = $post->toggleBookmark($userId, 'saved');

        return (bool) ($res['bookmarked'] ?? false);
    }

    public function savedPosts()
    {
        $user = auth('api')->user() ?? auth()->user();
        if (!$user) {
            return collect();
        }

        return app(\App\Modules\Interaction\Services\BookmarkService::class)
            ->getUserBookmarks($user, 'saved', 'post');
    }
}
