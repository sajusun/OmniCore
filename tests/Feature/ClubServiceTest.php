<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Club;
use App\Services\ClubService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClubServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ClubService $clubService;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->clubService = app(ClubService::class);

        $this->user = User::create([
            'name' => 'Club Owner',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
    }

    /**
     * Test storing a club with media.
     */
    public function test_can_store_club_with_media(): void
    {
        Storage::fake('public');

        $thumbnail = UploadedFile::fake()->image('thumbnail.jpg');
        $image1 = UploadedFile::fake()->image('image1.jpg');
        $image2 = UploadedFile::fake()->image('image2.jpg');
        $video1 = UploadedFile::fake()->create('video1.mp4', 500, 'video/mp4');
        $video2 = UploadedFile::fake()->create('video2.mp4', 1000, 'video/mp4');

        $data = [
            'name' => 'Royal Riders',
            'type' => 'motorcycle',
            'country' => 'Bangladesh',
            'state' => 'Dhaka',
            'city' => 'Dhaka',
            'description' => 'A group of passionate motorcycle riders.',
            'status' => 'published',
            'created_by' => $this->user->id,
            'thumbnail' => $thumbnail,
            'images' => [$image1, $image2],
            'video' => [$video1, $video2],
        ];

        $club = $this->clubService->store($data);

        // Verify database records
        $this->assertDatabaseHas('clubs', [
            'id' => $club->id,
            'name' => 'Royal Riders',
            'type' => 'motorcycle',
            'status' => 'published',
        ]);

        // Verify media collection attachments
        $this->assertEquals(1, $club->mediaCollection('thumbnail')->count());
        $this->assertEquals(2, $club->mediaCollection('images')->count());
        $this->assertEquals(2, $club->mediaCollection('video')->count());

        // Verify accessors on the Club model
        $this->assertNotNull($club->thumbnail);
        $this->assertNotEmpty($club->thumbnail_url);
        $this->assertCount(2, $club->images);
        $this->assertCount(2, $club->images_urls);
        $this->assertCount(2, $club->videos);
        $this->assertCount(2, $club->videos_urls);
    }

    /**
     * Test updating a club and updating its media.
     */
    public function test_can_update_club_and_media(): void
    {
        Storage::fake('public');

        // Create initial club
        $club = $this->clubService->store([
            'name' => 'Initial Club',
            'type' => 'car',
            'country' => 'US',
            'state' => 'NY',
            'city' => 'NYC',
            'created_by' => $this->user->id,
            'thumbnail' => UploadedFile::fake()->image('thumb1.jpg'),
        ]);

        $this->assertEquals(1, $club->mediaCollection('thumbnail')->count());

        $oldThumbnailUrl = $club->thumbnail_url;

        // Update club name and replace thumbnail
        $updatedClub = $this->clubService->update($club, [
            'name' => 'Updated Club',
            'thumbnail' => UploadedFile::fake()->image('thumb2.jpg'),
        ]);

        $this->assertDatabaseHas('clubs', [
            'id' => $club->id,
            'name' => 'Updated Club',
        ]);

        // Media of same collection should be replaced (old deleted, new uploaded)
        $this->assertEquals(1, $updatedClub->mediaCollection('thumbnail')->count());
        $this->assertNotEquals($oldThumbnailUrl, $updatedClub->thumbnail_url);
    }

    /**
     * Test deleting a club deletes all its media.
     */
    public function test_deleting_club_deletes_media(): void
    {
        Storage::fake('public');

        $club = $this->clubService->store([
            'name' => 'To Be Deleted',
            'type' => 'car',
            'country' => 'US',
            'state' => 'NY',
            'city' => 'NYC',
            'created_by' => $this->user->id,
            'thumbnail' => UploadedFile::fake()->image('thumb.jpg'),
            'images' => [UploadedFile::fake()->image('img.jpg')],
        ]);

        $mediaIds = $club->media->pluck('id')->toArray();
        $this->assertNotEmpty($mediaIds);

        // Delete club
        $this->clubService->delete($club);

        // Club should be gone
        $this->assertDatabaseMissing('clubs', ['id' => $club->id]);

        // Media records should be gone
        foreach ($mediaIds as $mediaId) {
            $this->assertDatabaseMissing('media', ['id' => $mediaId]);
        }
    }
}
