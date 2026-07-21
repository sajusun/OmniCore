<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostCommentLike;

class PostCommentService
{

    public function index(Post $post)
    {
        return $post->comments()
            ->with(['user', 'likes', 'replies.user', 'replies.likes'])
            ->withCount([
                'likes',
                'replies',
            ])->latest()->get();
    }
    public function store(Post $post, array $data): PostComment
    {
        return $post->comments()->create([
            'user_id' => auth()->id(),
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
        return $comment->delete();
    }

    public function reply(PostComment $comment, array $data): PostComment
    {
        return PostComment::create([
            'post_id' => $comment->post_id,
            'user_id' => auth()->id(),
            'parent_id' => $comment->id,
            'comment' => $data['comment'],
        ])->load('user');
    }

    public function toggleLike(PostComment $comment): bool
    {
        $like = PostCommentLike::where('comment_id', $comment->id)->where('user_id', auth()->id())->first();

        if ($like) {
            $like->delete();
            return false;
        }

        PostCommentLike::create([
            'comment_id' => $comment->id,
            'user_id'    => auth()->id(),
            'reaction'   => 'like',
        ]);

        return true;
    }

    public function replies(PostComment $comment)
{
    return $comment->replies()
        ->with(['user', 'likes','replies.user', 'replies.likes',])
        ->withCount([
            'likes',
            'replies',
        ])->latest()->paginate(10);
}
}
