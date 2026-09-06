<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Product\Models\Product;
use App\Modules\Review\Enums\ReviewStatus;
use App\Modules\Review\Models\Review;
use App\Modules\Review\Services\ReviewService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected User $secondUser;
    protected string $token;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        $this->user = User::factory()->create(['status' => 'active']);
        $this->secondUser = User::factory()->create(['status' => 'active']);
        $this->token = auth('api')->login($this->user);

        $this->product = Product::firstOrCreate(
            ['slug' => 'test-review-product'],
            [
                'name'  => 'Test Review Product',
                'sku'   => 'SKU-REV-001',
                'type'  => 'simple',
                'price' => 99.00,
            ]
        );
    }

    public function test_can_fetch_review_summary_for_product(): void
    {
        Review::create([
            'user_id'         => $this->user->id,
            'reviewable_type' => Product::class,
            'reviewable_id'   => $this->product->id,
            'rating'          => 5,
            'title'           => 'Great product',
            'comment'         => 'Loved using this product every day.',
            'status'          => ReviewStatus::APPROVED->value,
        ]);

        $response = $this->getJson('/api/v1/reviews/summary?reviewable_type=product&reviewable_id=' . $this->product->id);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'code'   => 200,
                'data'   => [
                    'average_rating' => 5.0,
                    'total_reviews'  => 1,
                ]
            ]);
    }

    public function test_user_can_submit_review_with_media(): void
    {
        Storage::fake('public');

        $photo = UploadedFile::fake()->create('unboxing.jpg', 150, 'image/jpeg');

        $payload = [
            'reviewable_type'  => 'product',
            'reviewable_id'    => $this->product->id,
            'rating'           => 4,
            'title'            => 'Solid purchase',
            'comment'          => 'Very good quality, arrived on time with neat packaging.',
            'criteria_ratings' => [
                'quality'  => 5,
                'value'    => 4,
                'delivery' => 4,
            ],
            'media'            => [$photo],
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/reviews', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'code'   => 201,
                'data'   => [
                    'rating'  => 4,
                    'title'   => 'Solid purchase',
                    'comment' => 'Very good quality, arrived on time with neat packaging.',
                ]
            ]);

        $this->assertDatabaseHas('reviews', [
            'user_id'         => $this->user->id,
            'reviewable_id'   => $this->product->id,
            'reviewable_type' => Product::class,
            'rating'          => 4,
        ]);

        $review = Review::where('user_id', $this->user->id)->first();
        $this->assertCount(1, $review->media);
    }

    public function test_can_list_approved_reviews_with_filters(): void
    {
        Review::create([
            'user_id'         => $this->user->id,
            'reviewable_type' => Product::class,
            'reviewable_id'   => $this->product->id,
            'rating'          => 5,
            'title'           => 'Five stars',
            'comment'         => 'Excellent experience overall.',
            'status'          => ReviewStatus::APPROVED->value,
        ]);

        Review::create([
            'user_id'         => $this->secondUser->id,
            'reviewable_type' => Product::class,
            'reviewable_id'   => $this->product->id,
            'rating'          => 3,
            'title'           => 'Average',
            'comment'         => 'Decent but expected better battery life.',
            'status'          => ReviewStatus::APPROVED->value,
        ]);

        // Query with 5-star filter
        $response = $this->getJson('/api/v1/reviews?reviewable_type=product&reviewable_id=' . $this->product->id . '&rating=5');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'code'   => 200,
            ])
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.rating', 5);
    }

    public function test_user_can_vote_review_helpfulness(): void
    {
        $review = Review::create([
            'user_id'         => $this->user->id,
            'reviewable_type' => Product::class,
            'reviewable_id'   => $this->product->id,
            'rating'          => 5,
            'title'           => 'In-depth review',
            'comment'         => 'Detailed review explaining all technical specifications.',
            'status'          => ReviewStatus::APPROVED->value,
        ]);

        $secondUserToken = auth('api')->login($this->secondUser);

        // Vote helpful
        $voteResponse = $this->withHeader('Authorization', 'Bearer ' . $secondUserToken)
            ->postJson('/api/v1/reviews/' . $review->id . '/vote', [
                'is_helpful' => true,
            ]);

        $voteResponse->assertStatus(200)
            ->assertJsonPath('data.helpful_count', 1)
            ->assertJsonPath('data.unhelpful_count', 0);

        $this->assertEquals(1, $review->fresh()->helpful_count);

        // Toggle to unhelpful
        $toggleResponse = $this->withHeader('Authorization', 'Bearer ' . $secondUserToken)
            ->postJson('/api/v1/reviews/' . $review->id . '/vote', [
                'is_helpful' => false,
            ]);

        $toggleResponse->assertStatus(200)
            ->assertJsonPath('data.helpful_count', 0)
            ->assertJsonPath('data.unhelpful_count', 1);

        $this->assertEquals(1, $review->fresh()->unhelpful_count);
    }

    public function test_vendor_can_reply_to_review(): void
    {
        $reviewService = app(ReviewService::class);

        $review = Review::create([
            'user_id'         => $this->user->id,
            'reviewable_type' => Product::class,
            'reviewable_id'   => $this->product->id,
            'rating'          => 4,
            'title'           => 'Good',
            'comment'         => 'Nice quality!',
            'status'          => ReviewStatus::APPROVED->value,
        ]);

        $updated = $reviewService->replyToReview($review, 'Thank you for your valuable feedback!');

        $this->assertEquals('Thank you for your valuable feedback!', $updated->vendor_reply);
        $this->assertNotNull($updated->vendor_replied_at);
    }

    public function test_user_can_delete_own_review(): void
    {
        $review = Review::create([
            'user_id'         => $this->user->id,
            'reviewable_type' => Product::class,
            'reviewable_id'   => $this->product->id,
            'rating'          => 3,
            'comment'         => 'Accidentally posted, will rewrite.',
            'status'          => ReviewStatus::APPROVED->value,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson('/api/v1/reviews/' . $review->id);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'code'   => 200,
            ]);

        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }
}
