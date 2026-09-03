<?php

namespace App\Modules\Post\Services;

use App\Modules\Post\Models\Post;
use App\Modules\Post\Models\PostComment;
use App\Modules\Post\Models\PostCommentLike;
use Illuminate\Support\Facades\Auth;

class PostCommentService
{
    public function index(Post $post)
    {
        return $post->comments()
            ->with(['user', 'likes', 'replies.user', 'replies.likes'])
            ->withCount([
                'likes',
                'replies',
            ])
            ->latest()
            ->get();
    }

    public function store(Post $post, array $data): PostComment
    {
        $userId = Auth::id() ?? auth('api')->id();

        return $post->comments()->create([
            'user_id' => $userId,
            'comment' => $data['comment'],
        ])->load('user');
    }

    public function update(PostComment $comment, array $data): PostComment
    {
        $comment->update([
            'comment' => $data['comment'],
            'edited_at' => now(),
        ]);

        return $comment->fresh()->load('user');
    }

    public function destroy(PostComment $comment): bool
    {
        return (bool) $comment->delete();
    }

    public function reply(PostComment $comment, array $data): PostComment
    {
        $userId = Auth::id() ?? auth('api')->id();

        return PostComment::create([
            'post_id' => $comment->post_id,
            'user_id' => $userId,
            'parent_id' => $comment->id,
            'comment' => $data['comment'],
        ])->load('user');
    }

    public function toggleLike(PostComment $comment): bool
    {
        $userId = Auth::id() ?? auth('api')->id();
        $like = PostCommentLike::where('comment_id', $comment->id)->where('user_id', $userId)->first();

        if ($like) {
            $like->delete();
            return false;
        }

        PostCommentLike::create([
            'comment_id' => $comment->id,
            'user_id' => $userId,
            'reaction' => 'like',
        ]);

        return true;
    }

    public function replies(PostComment $comment)
    {
        return $comment->replies()
            ->with(['user', 'likes', 'replies.user', 'replies.likes'])
            ->withCount([
                'likes',
                'replies',
            ])
            ->latest()
            ->paginate(10);
    }
}
