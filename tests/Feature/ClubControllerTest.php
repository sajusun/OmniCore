<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Club;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClubControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Club Owner',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $this->otherUser = User::create([
            'name' => 'Other User',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
    }

    /**
     * Test getting a list of clubs.
     */
    public function test_can_list_clubs(): void
    {
        Club::create([
            'name' => 'Car Club One',
            'type' => 'car',
            'country' => 'BD',
            'state' => 'Dhaka',
            'city' => 'Dhaka',
            'status' => 'published',
            'created_by' => $this->user->id,
        ]);

        Club::create([
            'name' => 'Motor Club Two',
            'type' => 'motorcycle',
            'country' => 'BD',
            'state' => 'Sylhet',
            'city' => 'Sylhet',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/clubs?type=car');

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonCount(1, 'data');
    }

    /**
     * Test storing a club via API.
     */
    public function test_can_store_club_via_api(): void
    {
        Storage::fake('public');

        $thumbnail = UploadedFile::fake()->image('club_thumb.jpg');
        $image1 = UploadedFile::fake()->image('pic1.jpg');
        $video1 = UploadedFile::fake()->create('vid1.mp4', 500, 'video/mp4');

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/clubs/store', [
                'club_name' => 'Super Bikers',
                'club_type' => 'motorcycle',
                'country' => 'Bangladesh',
                'state' => 'Chittagong',
                'city' => 'Coxs Bazar',
                'description' => 'Passtionate riders',
                'status' => 'published',
                'thumbnail' => $thumbnail,
                'images' => [$image1],
                'videos' => [$video1],
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.name', 'Super Bikers');

        $clubId = $response->json('data.id');
        $this->assertDatabaseHas('clubs', [
            'id' => $clubId,
            'name' => 'Super Bikers',
        ]);
    }

    /**
     * Test showing a club details.
     */
    public function test_can_show_club(): void
    {
        $club = Club::create([
            'name' => 'Vintage Cars',
            'type' => 'car',
            'country' => 'US',
            'state' => 'CA',
            'city' => 'LA',
            'status' => 'published',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson("/api/clubs/{$club->id}/show");

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.name', 'Vintage Cars');
    }

    /**
     * Test updating a club.
     */
    public function test_can_update_club(): void
    {
        $club = Club::create([
            'name' => 'Vintage Cars',
            'type' => 'car',
            'country' => 'US',
            'state' => 'CA',
            'city' => 'LA',
            'status' => 'published',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/clubs/{$club->id}/update", [
                'name' => 'Updated Vintage Cars',
                'country' => 'USA',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.name', 'Updated Vintage Cars');

        // Test other user cannot update
        $unauthorizedResponse = $this->actingAs($this->otherUser, 'api')
            ->postJson("/api/clubs/{$club->id}/update", [
                'name' => 'Hacker Club',
            ]);

        $unauthorizedResponse->assertStatus(403);
    }

    /**
     * Test deleting a club.
     */
    public function test_can_delete_club(): void
    {
        $club = Club::create([
            'name' => 'To Delete',
            'type' => 'car',
            'country' => 'US',
            'state' => 'CA',
            'city' => 'LA',
            'status' => 'published',
            'created_by' => $this->user->id,
        ]);

        // Unauthorized user cannot delete
        $unauthorizedResponse = $this->actingAs($this->otherUser, 'api')
            ->deleteJson("/api/clubs/{$club->id}/delete");

        $unauthorizedResponse->assertStatus(403);

        // Owner can delete
        $response = $this->actingAs($this->user, 'api')
            ->deleteJson("/api/clubs/{$club->id}/delete");

        $response->assertStatus(200)
            ->assertJsonPath('status', true);

        $this->assertDatabaseMissing('clubs', ['id' => $club->id]);
    }
}
