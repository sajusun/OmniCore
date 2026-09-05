<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Interaction\Models\Comment;
use App\Modules\Interaction\Models\Like;
use App\Modules\Interaction\Models\ShareLink;
use App\Modules\Interaction\Services\CommentService;
use App\Modules\Interaction\Services\LikeService;
use App\Modules\Interaction\Services\ShareService;
use App\Modules\Product\Models\Product;
use Carbon\Carbon;
use Tests\TestCase;

class InteractionModuleTest extends TestCase
{
    protected User $user;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::first() ?? User::create([
            'name' => 'Interaction Tester',
            'email' => 'interaction_tester_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->product = Product::create([
            'name' => 'MacBook Pro M3 Max ' . uniqid(),
            'slug' => 'macbook-pro-m3-max-' . uniqid(),
            'sku' => 'SKU-' . uniqid(),
            'price' => 2499.00,
        ]);
    }

    /**
     * Test creating a polymorphic comment and reply.
     */
    public function test_can_comment_and_reply_on_model(): void
    {
        /** @var CommentService $commentService */
        $commentService = app(CommentService::class);

        // 1. Create Top-level Comment
        $comment = $commentService->createComment($this->product, $this->user, 'Amazing laptop!');
        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals('Amazing laptop!', $comment->body);
        $this->assertEquals($this->product->id, $comment->commentable_id);
        $this->assertNull($comment->parent_id);

        // 2. Create Reply under Comment
        $reply = $commentService->createReply($comment, $this->user, 'I totally agree with you.');
        $this->assertInstanceOf(Comment::class, $reply);
        $this->assertEquals($comment->id, $reply->parent_id);
        $this->assertTrue($reply->isReply());

        // 3. Verify parent comment replies_count
        $comment->refresh();
        $this->assertEquals(1, $comment->replies_count);
    }

    /**
     * Test liking and unliking a model (toggle).
     */
    public function test_can_toggle_like_on_model(): void
    {
        /** @var LikeService $likeService */
        $likeService = app(LikeService::class);

        // 1. Like
        $res1 = $likeService->toggleLike($this->product, $this->user, 'love');
        $this->assertTrue($res1['liked']);
        $this->assertEquals('love', $res1['type']);
        $this->assertEquals(1, $res1['likes_count']);
        $this->assertTrue($this->product->isLikedBy($this->user));

        // 2. Unlike (Toggle)
        $res2 = $likeService->toggleLike($this->product, $this->user);
        $this->assertFalse($res2['liked']);
        $this->assertEquals(0, $res2['likes_count']);
        $this->assertFalse($this->product->isLikedBy($this->user));
    }

    /**
     * Test liking a comment itself.
     */
    public function test_can_like_a_comment(): void
    {
        $comment = $this->product->addComment('Great review!', $this->user);

        // Like the comment
        $res = $comment->toggleLike($this->user);
        $this->assertTrue($res['liked']);
        $this->assertTrue($comment->isLikedBy($this->user));

        $comment->refresh();
        $this->assertEquals(1, $comment->likes_count);
    }

    /**
     * Test public SEO share link generation and resolution.
     */
    public function test_public_seo_share_link(): void
    {
        /** @var ShareService $shareService */
        $shareService = app(ShareService::class);

        $shareLink = $shareService->generatePublicLink($this->product, $this->user);
        $this->assertInstanceOf(ShareLink::class, $shareLink);
        $this->assertEquals('public', $shareLink->type);
        $this->assertEquals($this->product->slug, $shareLink->slug);

        // Resolve public slug
        $resolvedModel = $shareService->resolvePublicSlug('product', $shareLink->slug);
        $this->assertNotNull($resolvedModel);
        $this->assertEquals($this->product->id, $resolvedModel->id);
    }

    /**
     * Test private expiring & single-use share links.
     */
    public function test_private_expiring_and_single_use_share_link(): void
    {
        /** @var ShareService $shareService */
        $shareService = app(ShareService::class);

        // 1. Single-use Link (max 1 click)
        $singleUseLink = $shareService->generatePrivateLink($this->product, $this->user, null, 1);
        $this->assertTrue($singleUseLink->isValid());

        // First access -> Valid & records click
        $resolved = $shareService->resolvePrivateToken($singleUseLink->token);
        $this->assertNotNull($resolved);
        $this->assertEquals(1, $resolved->click_count);

        // Second access -> Invalid (Limit reached)
        $secondAccess = $shareService->resolvePrivateToken($singleUseLink->token);
        $this->assertNull($secondAccess);

        // 2. Expired Link
        $expiredLink = $shareService->generatePrivateLink(
            $this->product,
            $this->user,
            Carbon::now()->subMinutes(10), // Expired 10 mins ago
            null
        );
        $this->assertTrue($expiredLink->isExpired());
        $this->assertFalse($expiredLink->isValid());

        $expiredResolved = $shareService->resolvePrivateToken($expiredLink->token);
        $this->assertNull($expiredResolved);
    }

    /**
     * Test recording views and anti-spam cooldown logic.
     */
    public function test_view_recording_with_anti_spam_cooldown(): void
    {
        // 1. Record first view for user
        $firstView = $this->product->recordView($this->user, '127.0.0.1', 60);
        $this->assertTrue($firstView);
        $this->assertEquals(1, $this->product->viewsCount());
        $this->assertTrue($this->product->isViewedBy($this->user));

        // 2. Immediate second view from same user -> Ignored (Cooldown active)
        $secondView = $this->product->recordView($this->user, '127.0.0.1', 60);
        $this->assertFalse($secondView);
        $this->assertEquals(1, $this->product->viewsCount());

        // 3. Guest view from different IP -> Allowed
        $guestView = $this->product->recordView(null, '192.168.1.100', 60);
        $this->assertTrue($guestView);
        $this->assertEquals(2, $this->product->viewsCount());
        $this->assertEquals(2, $this->product->uniqueViewsCount());
    }
}
