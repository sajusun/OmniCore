<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\User;
use App\Models\ClubMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClubMembershipTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $userA;
    protected User $userB;
    protected Club $club;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::create([
            'name' => 'Owner', 'email' => 'owner@test.com',
            'password' => bcrypt('pass'), 'status' => 'active',
        ]);
        $this->userA = User::create([
            'name' => 'User A', 'email' => 'usera@test.com',
            'password' => bcrypt('pass'), 'status' => 'active',
        ]);
        $this->userB = User::create([
            'name' => 'User B', 'email' => 'userb@test.com',
            'password' => bcrypt('pass'), 'status' => 'active',
        ]);

        // Create club via API so creator is auto-added as admin member
        $response = $this->actingAs($this->owner, 'api')
            ->postJson('/api/clubs/store', [
                'name' => 'Test Club', 'type' => 'car',
                'country' => 'BD', 'state' => 'Dhaka', 'city' => 'Dhaka',
                'status' => 'published',
            ]);

        $this->club = Club::find($response->json('data.id'));
    }

    public function test_creator_is_auto_added_as_admin_member(): void
    {
        $this->assertDatabaseHas('club_members', [
            'club_id' => $this->club->id,
            'user_id' => $this->owner->id,
            'role'    => 'admin',
            'status'  => 'approved',
        ]);
    }

    public function test_user_can_join_club(): void
    {
        $response = $this->actingAs($this->userA, 'api')
            ->postJson("/api/clubs/{$this->club->id}/join");

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'approved');

        $this->assertDatabaseHas('club_members', [
            'club_id' => $this->club->id,
            'user_id' => $this->userA->id,
            'status'  => 'approved',
        ]);
    }

    public function test_joining_twice_returns_existing_membership(): void
    {
        $this->actingAs($this->userA, 'api')->postJson("/api/clubs/{$this->club->id}/join");
        $response = $this->actingAs($this->userA, 'api')->postJson("/api/clubs/{$this->club->id}/join");

        $response->assertStatus(201);
        $this->assertEquals(1, ClubMember::where([
            'club_id' => $this->club->id,
            'user_id' => $this->userA->id,
        ])->count());
    }

    public function test_club_resource_includes_members_count_and_membership_info(): void
    {
        $this->actingAs($this->userA, 'api')->postJson("/api/clubs/{$this->club->id}/join");

        $response = $this->actingAs($this->userA, 'api')
            ->getJson("/api/clubs/{$this->club->id}/show");

        $response->assertStatus(200)
            ->assertJsonPath('data.members_count', 2)   // owner + userA
            ->assertJsonPath('data.is_member', true)
            ->assertJsonPath('data.user_role', 'member')
            ->assertJsonPath('data.user_membership_status', 'approved');
    }

    public function test_user_can_leave_club(): void
    {
        $this->actingAs($this->userA, 'api')->postJson("/api/clubs/{$this->club->id}/join");

        $response = $this->actingAs($this->userA, 'api')
            ->deleteJson("/api/clubs/{$this->club->id}/leave");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('club_members', [
            'club_id' => $this->club->id,
            'user_id' => $this->userA->id,
        ]);
    }

    public function test_creator_cannot_leave_club(): void
    {
        $response = $this->actingAs($this->owner, 'api')
            ->deleteJson("/api/clubs/{$this->club->id}/leave");

        $response->assertStatus(422);
    }

    public function test_can_list_members(): void
    {
        $this->actingAs($this->userA, 'api')->postJson("/api/clubs/{$this->club->id}/join");
        $this->actingAs($this->userB, 'api')->postJson("/api/clubs/{$this->club->id}/join");

        $response = $this->actingAs($this->owner, 'api')
            ->getJson("/api/clubs/{$this->club->id}/members");

        $response->assertStatus(200);
        // owner + userA + userB = 3 members
        $this->assertGreaterThanOrEqual(3, $response->json('pagination.total'));
    }

    public function test_admin_can_remove_a_member(): void
    {
        $this->actingAs($this->userA, 'api')->postJson("/api/clubs/{$this->club->id}/join");

        $response = $this->actingAs($this->owner, 'api')
            ->deleteJson("/api/clubs/{$this->club->id}/members/{$this->userA->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('club_members', [
            'club_id' => $this->club->id,
            'user_id' => $this->userA->id,
        ]);
    }

    public function test_non_admin_cannot_remove_a_member(): void
    {
        $this->actingAs($this->userA, 'api')->postJson("/api/clubs/{$this->club->id}/join");
        $this->actingAs($this->userB, 'api')->postJson("/api/clubs/{$this->club->id}/join");

        $response = $this->actingAs($this->userA, 'api')
            ->deleteJson("/api/clubs/{$this->club->id}/members/{$this->userB->id}");

        $response->assertStatus(422);
    }

    public function test_admin_can_approve_pending_member(): void
    {
        // Manually insert a pending membership to simulate future approval flow
        ClubMember::create([
            'club_id'   => $this->club->id,
            'user_id'   => $this->userA->id,
            'role'      => 'member',
            'status'    => 'pending',
            'joined_at' => now(),
        ]);

        $response = $this->actingAs($this->owner, 'api')
            ->postJson("/api/clubs/{$this->club->id}/members/{$this->userA->id}/approve");

        $response->assertStatus(200);
        $this->assertDatabaseHas('club_members', [
            'club_id' => $this->club->id,
            'user_id' => $this->userA->id,
            'status'  => 'approved',
        ]);
    }

    public function test_admin_can_reject_pending_member(): void
    {
        ClubMember::create([
            'club_id'   => $this->club->id,
            'user_id'   => $this->userB->id,
            'role'      => 'member',
            'status'    => 'pending',
            'joined_at' => now(),
        ]);

        $response = $this->actingAs($this->owner, 'api')
            ->postJson("/api/clubs/{$this->club->id}/members/{$this->userB->id}/reject");

        $response->assertStatus(200);
        $this->assertDatabaseHas('club_members', [
            'club_id' => $this->club->id,
            'user_id' => $this->userB->id,
            'status'  => 'rejected',
        ]);
    }
}
