<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Interaction\Models\Bookmark;
use App\Modules\Interaction\Models\Comment;
use App\Modules\Interaction\Models\Like;
use App\Modules\Interaction\Models\ShareLink;
use App\Modules\Interaction\Services\BookmarkService;
use App\Modules\Interaction\Services\CommentService;
use App\Modules\Interaction\Services\LikeService;
use App\Modules\Interaction\Services\ShareService;
use App\Modules\Post\Models\Post;
use App\Modules\Product\Models\Product;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InteractionModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Product $product;
    protected Post $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
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

        $this->post = Post::create([
            'user_id' => $this->user->id,
            'title' => 'Laravel 11 Interaction System ' . uniqid(),
            'slug' => 'laravel-11-interaction-system-' . uniqid(),
            'content' => 'Comprehensive polymorphic interaction module test content.',
            'status' => 'published',
            'visibility' => 'public',
        ]);
    }

    /**
     * Test creating a polymorphic comment and reply on Product.
     */
    public function test_can_comment_and_reply_on_product(): void
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
     * Test creating comments and replies on Post model.
     */
    public function test_can_comment_and_reply_on_post(): void
    {
        $comment = $this->post->addComment('Incredible architectural design!', $this->user);
        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals('post', $comment->commentable_type);
        $this->assertEquals($this->post->id, $comment->commentable_id);

        $reply = $comment->reply('Thanks for the positive feedback!', $this->user);
        $this->assertInstanceOf(Comment::class, $reply);
        $this->assertEquals($comment->id, $reply->parent_id);

        $comment->refresh();
        $this->assertEquals(1, $comment->replies_count);
    }

    /**
     * Test liking and unliking models (toggle).
     */
    public function test_can_toggle_like_on_product_and_post(): void
    {
        /** @var LikeService $likeService */
        $likeService = app(LikeService::class);

        // 1. Like Product
        $res1 = $likeService->toggleLike($this->product, $this->user, 'love');
        $this->assertTrue($res1['liked']);
        $this->assertEquals('love', $res1['type']);
        $this->assertEquals(1, $res1['likes_count']);
        $this->assertTrue($this->product->isLikedBy($this->user));

        // 2. Unlike Product (Toggle)
        $res2 = $likeService->toggleLike($this->product, $this->user);
        $this->assertFalse($res2['liked']);
        $this->assertEquals(0, $res2['likes_count']);
        $this->assertFalse($this->product->isLikedBy($this->user));

        // 3. Like Post
        $postRes1 = $this->post->toggleLike($this->user, 'fire');
        $this->assertTrue($postRes1['liked']);
        $this->assertEquals('fire', $postRes1['type']);
        $this->assertTrue($this->post->isLikedBy($this->user));

        // 4. Unlike Post
        $postRes2 = $this->post->toggleLike($this->user);
        $this->assertFalse($postRes2['liked']);
        $this->assertFalse($this->post->isLikedBy($this->user));
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
     * Test public SEO share link generation and resolution for Product & Post.
     */
    public function test_public_seo_share_link(): void
    {
        /** @var ShareService $shareService */
        $shareService = app(ShareService::class);

        // Product SEO Share Link
        $productShareLink = $shareService->generatePublicLink($this->product, $this->user);
        $this->assertInstanceOf(ShareLink::class, $productShareLink);
        $this->assertEquals('public', $productShareLink->type);
        $this->assertEquals($this->product->slug, $productShareLink->slug);

        $resolvedProduct = $shareService->resolvePublicSlug('product', $productShareLink->slug);
        $this->assertNotNull($resolvedProduct);
        $this->assertEquals($this->product->id, $resolvedProduct->id);

        // Post SEO Share Link
        $postShareLink = $shareService->generatePublicLink($this->post, $this->user);
        $this->assertInstanceOf(ShareLink::class, $postShareLink);
        $this->assertEquals('public', $postShareLink->type);
        $this->assertEquals($this->post->slug, $postShareLink->slug);

        $resolvedPost = $shareService->resolvePublicSlug('post', $postShareLink->slug);
        $this->assertNotNull($resolvedPost);
        $this->assertEquals($this->post->id, $resolvedPost->id);
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
     * Test recording views and anti-spam cooldown logic for Product & Post.
     */
    public function test_view_recording_with_anti_spam_cooldown(): void
    {
        // 1. Record first view for user on Product
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

        // 4. Record view on Post
        $postView = $this->post->recordView($this->user, '127.0.0.1', 60);
        $this->assertTrue($postView);
        $this->assertEquals(1, $this->post->viewsCount());
    }

    /**
     * Test Universal Bookmark / Wishlist / Save functionality across Product and Post.
     */
    public function test_universal_bookmarks_and_wishlists(): void
    {
        /** @var BookmarkService $bookmarkService */
        $bookmarkService = app(BookmarkService::class);

        // 1. Add Product to Wishlist collection
        $wishlistRes = $this->product->toggleBookmark($this->user, 'wishlist');
        $this->assertTrue($wishlistRes['bookmarked']);
        $this->assertEquals('wishlist', $wishlistRes['collection']);
        $this->assertEquals(1, $wishlistRes['bookmarks_count']);
        $this->assertTrue($this->product->isBookmarkedBy($this->user, 'wishlist'));
        $this->assertFalse($this->product->isBookmarkedBy($this->user, 'default')); // different collection

        // 2. Save Post to 'saved' collection
        $postSaveRes = $this->post->toggleBookmark($this->user, 'saved');
        $this->assertTrue($postSaveRes['bookmarked']);
        $this->assertEquals('saved', $postSaveRes['collection']);
        $this->assertEquals(1, $postSaveRes['bookmarks_count']);
        $this->assertTrue($this->post->isBookmarkedBy($this->user, 'saved'));

        // 3. Retrieve user bookmarks filtered by collection
        $userWishlists = $bookmarkService->getUserBookmarks($this->user, 'wishlist');
        $this->assertEquals(1, $userWishlists->count());
        $this->assertEquals($this->product->id, $userWishlists->first()->bookmarkable_id);

        $userSavedPosts = $bookmarkService->getUserBookmarks($this->user, 'saved');
        $this->assertEquals(1, $userSavedPosts->count());
        $this->assertEquals($this->post->id, $userSavedPosts->first()->bookmarkable_id);

        // 4. Toggle bookmark off (remove from wishlist)
        $removeWishlist = $this->product->toggleBookmark($this->user, 'wishlist');
        $this->assertFalse($removeWishlist['bookmarked']);
        $this->assertEquals(0, $removeWishlist['bookmarks_count']);
        $this->assertFalse($this->product->isBookmarkedBy($this->user, 'wishlist'));
    }

    /**
     * Test API Endpoints for Bookmarks and Interactivity.
     */
    public function test_bookmark_api_endpoints(): void
    {
        // 1. Toggle Product to wishlist via API
        $response = $this->actingAs($this->user, 'api')->postJson('/api/interactions/bookmarks/toggle', [
            'type' => 'product',
            'id' => $this->product->id,
            'collection' => 'wishlist',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'bookmarked' => true,
                    'collection' => 'wishlist',
                    'bookmarks_count' => 1,
                ],
            ]);

        // 2. Fetch User Bookmarks via API
        $listResponse = $this->actingAs($this->user, 'api')->getJson('/api/interactions/bookmarks?collection=wishlist');
        $listResponse->assertStatus(200)
            ->assertJson([
                'status' => true,
                'code' => 200,
            ])
            ->assertJsonStructure([
                'data',
                'pagination',
            ]);
    }
}
