<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Image;
use App\Enums\PostType;
use App\Helpers\Helper;
use App\Models\PostLike;
use App\Models\SavedPost;
use Illuminate\Support\Str;
use App\Enums\PostStatusEnum;
use App\Enums\PostVisibilityEnum;
use GuzzleHttp\Psr7\UploadedFile;
use App\Models\PostVisibilityUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostService
{

    public function index(): LengthAwarePaginator
    {
        return Post::query()
            ->with(['user', 'images', 'sharedPost.user', 'sharedPost.images',])
            ->withCount(['likes', 'comments', 'shares', 'views'])
            ->where('user_id', auth()->id())->latest()->paginate(15);
    }

    public function show(Post $post): Post
    {
        return $post->load([
            'user',
            'images',
            'sharedPost.user',
            'sharedPost.images',
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
                $thumbnail = Helper::fileUpload(
                    $data['thumbnail'],
                    'post',
                    time() . '_' . getFileName($data['thumbnail'])
                );
            }

            $post = Post::create([
                'user_id'      => Auth::id(),
                'title'        => $data['title'] ?? null,
                'slug'         => Helper::makeSlug(Post::class, $data['title'] ?? Str::random()),
                'content'      => $data['content'],
                'thumbnail'    => $thumbnail,
                'visibility'   => $data['visibility'],
                'type'         => $data['type'] ?? 'post',
                'status'       => 'published',
                'shared_post_id' => $data['shared_post_id'] ?? null,
            ]);

            if ($post->visibility === PostVisibilityEnum::FRIENDS->value && !empty($data['friend_ids'])) {
                $post->visibleUsers()->sync($data['friend_ids']);
            }

            if (! empty($data['media'])) {

                foreach ($data['media'] as $key => $media) {


                    $mimeType = $media->getMimeType();
                    $type = str_starts_with($mimeType, 'video/') ? 'video' : 'image';

                    $mediaPath = Helper::fileUpload(
                        $media,
                        'post',
                        'post_' . time() . '_' . Str::random(10)
                    );


                    Image::create([
                        'post_id'       => $post->id,
                        'path'          => $mediaPath,
                        'type'          => $type,
                        'sort_order'    => $key + 1,
                    ]);
                }
            }

            return $post->load(['user', 'images',]);
        });
    }

    public function update(Post $post, array $data): Post
    {
        return DB::transaction(function () use ($post, $data) {

            if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {

                if (!empty($post->thumbnail)) {
                    Helper::fileDelete($post->thumbnail);
                }

                $post->thumbnail = Helper::fileUpload(
                    $data['thumbnail'],
                    'post',
                    time() . '_' . getFileName($data['thumbnail'])
                );
            }

            $post->update([
                'title'          => $data['title'] ?? $post->title,
                'content'        => $data['content'],
                'visibility'     => $data['visibility'],
                'type'           => $data['type'] ?? $post->type,
                'shared_post_id' => $data['shared_post_id'] ?? $post->shared_post_id,
            ]);

            if ($post->visibility === PostVisibilityEnum::FRIENDS->value && !empty($data['friend_ids'])) {
                $post->visibleUsers()->sync($data['friend_ids']);
            }

            if (!empty($data['media'])) {

                $sortOrder = $post->images()->max('sort_order') ?? 0;

                foreach ($data['media'] as $media) {

                    $mimeType = $media->getMimeType();
                    $type = str_starts_with($mimeType, 'video/') ? 'video' : 'image';

                    $mediaPath = Helper::fileUpload(
                        $media,
                        'post',
                        'post_' . time() . '_' . Str::random(10)
                    );

                    $post->images()->create([
                        'path' => $mediaPath,
                        'type' => $type,
                        'sort_order' => ++$sortOrder,
                    ]);
                }
            }

            return $post->load(['user', 'images',]);
        });
    }

    public function destroy(Post $post): bool
    {
        return DB::transaction(function () use ($post) {

            // Delete Thumbnail
            if (!empty($post->thumbnail)) {
                Helper::fileDelete($post->thumbnail);
            }

            // Delete Post Images
            foreach ($post->images as $image) {

                if (!empty($image->image)) {
                    Helper::fileDelete($image->image);
                }

                $image->delete();
            }

            // Soft Delete Post
            return $post->delete();
        });
    }

    // public function feed(): LengthAwarePaginator
    // {
    //     return Post::query()
    //         ->with([
    //             'user',
    //             'images',
    //             'sharedPost.user',
    //             'sharedPost.images',
    //         ])
    //         ->withCount([
    //             'likes',
    //             'comments',
    //             'shares',
    //             'views',
    //         ])
    //         ->where('status', 'published')
    //         ->where('visibility', 'public')
    //         ->latest()
    //         ->paginate(15);
    // }
    public function feed(): LengthAwarePaginator
    {
        $authId = Auth::id();

        return Post::query()
            ->with([
                'user',
                'images',
                'visibleUsers',
                'sharedPost.user',
                'sharedPost.images',
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
                'visibility'     => $data['visibility'],
                'type'           => PostType::SHARED,
                'status'         => $post->status,
            ]);

            return $sharedPost->load(['user', 'sharedPost.user', 'sharedPost.images',]);
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
        return auth()->user()
            ->savedPosts()->with(['user', 'images', 'sharedPost.user', 'sharedPost.images'])->latest()->paginate(15);
    }
}
