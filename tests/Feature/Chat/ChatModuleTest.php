<?php

namespace Tests\Feature\Chat;

use App\Models\User;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Models\ChatParticipant;
use App\Models\UserBlock;
use App\Enums\Chat\ChatRoomTypeEnum;
use App\Enums\Chat\MessageTypeEnum;
use App\Enums\Chat\ParticipantRoleEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ChatModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user1;
    protected User $user2;
    protected User $user3;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users manually to avoid factory dependency issues
        $this->user1 = User::create([
            'name' => 'User One',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $this->user2 = User::create([
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $this->user3 = User::create([
            'name' => 'User Three',
            'email' => 'user3@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
    }

    /**
     * Test creating a single chat room.
     */
    public function test_can_create_single_room(): void
    {
        $response = $this->actingAs($this->user1, 'api')
            ->postJson('/api/chat/rooms/single', [
                'user_id' => $this->user2->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', ChatRoomTypeEnum::SINGLE->value);

        // Subsequent call to create single room returns the same room
        $response2 = $this->actingAs($this->user1, 'api')
            ->postJson('/api/chat/rooms/single', [
                'user_id' => $this->user2->id,
            ]);

        $response2->assertStatus(201)
            ->assertJsonPath('data.id', $response->json('data.id'));
    }

    /**
     * Test block system prevents room creation and messaging.
     */
    public function test_block_system_restricts_interaction(): void
    {
        // User 2 blocks User 1
        $this->actingAs($this->user2, 'api')
            ->postJson("/api/chat/block/{$this->user1->id}")
            ->assertOk();

        $this->assertTrue(
            UserBlock::where('user_id', $this->user2->id)
                ->where('blocked_user_id', $this->user1->id)
                ->exists()
        );

        // User 1 cannot create single room with User 2
        $response = $this->actingAs($this->user1, 'api')
            ->postJson('/api/chat/rooms/single', [
                'user_id' => $this->user2->id,
            ]);

        $response->assertStatus(403);

        // Let's create a single room before block to test message block
        // Unblock first, create room, then block again
        $this->actingAs($this->user2, 'api')
            ->deleteJson("/api/chat/unblock/{$this->user1->id}")
            ->assertOk();

        $roomResponse = $this->actingAs($this->user1, 'api')
            ->postJson('/api/chat/rooms/single', [
                'user_id' => $this->user2->id,
            ]);

        $roomId = $roomResponse->json('data.id');

        // Block again
        $this->actingAs($this->user2, 'api')
            ->postJson("/api/chat/block/{$this->user1->id}")
            ->assertOk();

        // User 1 (blocked user) cannot send message to User 2 (blocker) in the room
        $messageResponse = $this->actingAs($this->user1, 'api')
            ->postJson('/api/chat/messages', [
                'chat_room_id' => $roomId,
                'message' => 'Hello',
            ]);

        $messageResponse->assertStatus(403);
    }

    /**
     * Test creating a group and managing participants.
     */
    public function test_can_manage_group_and_participants(): void
    {
        $response = $this->actingAs($this->user1, 'api')
            ->postJson('/api/chat/rooms/group', [
                'name' => 'Team Chat',
                'description' => 'A team group chat room',
                'participant_ids' => [$this->user2->id],
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Team Chat')
            ->assertJsonPath('data.type', ChatRoomTypeEnum::GROUP->value);

        $roomId = $response->json('data.id');

        // Creator (user1) can add user3
        $this->actingAs($this->user1, 'api')
            ->postJson("/api/chat/rooms/{$roomId}/participants", [
                'user_ids' => [$this->user3->id],
            ])
            ->assertOk();

        $this->assertDatabaseHas('chat_participants', [
            'chat_room_id' => $roomId,
            'user_id' => $this->user3->id,
        ]);

        // User 3 can leave
        $this->actingAs($this->user3, 'api')
            ->postJson("/api/chat/rooms/{$roomId}/leave")
            ->assertOk();

        $this->assertDatabaseMissing('chat_participants', [
            'chat_room_id' => $roomId,
            'user_id' => $this->user3->id,
        ]);

        // Creator can remove User 2
        $this->actingAs($this->user1, 'api')
            ->deleteJson("/api/chat/rooms/{$roomId}/participants/{$this->user2->id}")
            ->assertOk();

        $this->assertDatabaseMissing('chat_participants', [
            'chat_room_id' => $roomId,
            'user_id' => $this->user2->id,
        ]);
    }

    /**
     * Test creating a channel and joining it.
     */
    public function test_can_manage_channel(): void
    {
        $response = $this->actingAs($this->user1, 'api')
            ->postJson('/api/chat/rooms/channel', [
                'name' => 'News Channel',
                'description' => 'Announcements channel',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', ChatRoomTypeEnum::CHANNEL->value);

        $roomId = $response->json('data.id');

        // User 2 joins the channel
        $this->actingAs($this->user2, 'api')
            ->postJson("/api/chat/channels/{$roomId}/join")
            ->assertOk();

        $this->assertDatabaseHas('chat_participants', [
            'chat_room_id' => $roomId,
            'user_id' => $this->user2->id,
            'role' => ParticipantRoleEnum::MEMBER->value,
        ]);

        // User 2 cannot send a message in a channel (since they are a regular member)
        $msgResponse = $this->actingAs($this->user2, 'api')
            ->postJson('/api/chat/messages', [
                'chat_room_id' => $roomId,
                'message' => 'Hey',
            ]);

        $msgResponse->assertStatus(403);

        // Creator/Owner (User 1) CAN send a message in the channel
        $msgResponse2 = $this->actingAs($this->user1, 'api')
            ->postJson('/api/chat/messages', [
                'chat_room_id' => $roomId,
                'message' => 'Important announcement!',
            ]);

        $msgResponse2->assertStatus(201);
    }

    /**
     * Test sending, updating, and deleting messages (including file upload).
     */
    public function test_can_manage_messages_with_media(): void
    {
        Storage::fake('public');

        // Setup single room
        $room = ChatRoom::create(['type' => ChatRoomTypeEnum::SINGLE->value]);
        ChatParticipant::create(['chat_room_id' => $room->id, 'user_id' => $this->user1->id, 'role' => ParticipantRoleEnum::OWNER->value]);
        ChatParticipant::create(['chat_room_id' => $room->id, 'user_id' => $this->user2->id, 'role' => ParticipantRoleEnum::MEMBER->value]);

        // 1. Send Text message
        $response = $this->actingAs($this->user1, 'api')
            ->postJson('/api/chat/messages', [
                'chat_room_id' => $room->id,
                'message' => 'Hello User 2',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.message', 'Hello User 2')
            ->assertJsonPath('data.message_type', MessageTypeEnum::TEXT->value);

        $messageId = $response->json('data.id');

        // 2. Update message
        $updateResponse = $this->actingAs($this->user1, 'api')
            ->patchJson("/api/chat/messages/{$messageId}", [
                'message' => 'Hello User 2 (edited)',
            ]);

        $updateResponse->assertOk()
            ->assertJsonPath('data.message', 'Hello User 2 (edited)')
            ->assertJsonPath('data.is_edited', true);

        // 3. Send Media message
        $file = UploadedFile::fake()->image('photo.jpg');

        $mediaResponse = $this->actingAs($this->user1, 'api')
            ->postJson('/api/chat/messages', [
                'chat_room_id' => $room->id,
                'files' => [$file],
            ]);

        $mediaResponse->assertStatus(201)
            ->assertJsonPath('data.message_type', MessageTypeEnum::IMAGE->value)
            ->assertJsonCount(1, 'data.media');

        $mediaMessageId = $mediaResponse->json('data.id');
        $mediaPath = $mediaResponse->json('data.media.0.url');

        // 4. Delete message (and attached media)
        $this->actingAs($this->user1, 'api')
            ->deleteJson("/api/chat/messages/{$mediaMessageId}")
            ->assertOk();

        $this->assertSoftDeleted('messages', ['id' => $mediaMessageId]);
    }

    /**
     * Test chat room settings updates.
     */
    public function test_can_manage_room_settings(): void
    {
        // Setup room
        $room = ChatRoom::create(['type' => ChatRoomTypeEnum::GROUP->value]);
        ChatParticipant::create(['chat_room_id' => $room->id, 'user_id' => $this->user1->id, 'role' => ParticipantRoleEnum::OWNER->value]);

        // Toggle notification
        $this->actingAs($this->user1, 'api')
            ->patchJson("/api/chat/rooms/{$room->id}/settings/notification", ['enabled' => false])
            ->assertOk();

        $this->assertDatabaseHas('chat_participants', [
            'chat_room_id' => $room->id,
            'user_id' => $this->user1->id,
            'notification_enabled' => false,
        ]);

        // Toggle sound
        $this->actingAs($this->user1, 'api')
            ->patchJson("/api/chat/rooms/{$room->id}/settings/sound", ['enabled' => false])
            ->assertOk();

        $this->assertDatabaseHas('chat_participants', [
            'chat_room_id' => $room->id,
            'user_id' => $this->user1->id,
            'sound_enabled' => false,
        ]);

        // Mute room
        $muteTime = now()->addHour()->toIso8601String();
        $this->actingAs($this->user1, 'api')
            ->patchJson("/api/chat/rooms/{$room->id}/settings/mute", ['mute_until' => $muteTime])
            ->assertOk();

        // Unmute room
        $this->actingAs($this->user1, 'api')
            ->deleteJson("/api/chat/rooms/{$room->id}/settings/mute")
            ->assertOk();

        $this->assertDatabaseHas('chat_participants', [
            'chat_room_id' => $room->id,
            'user_id' => $this->user1->id,
            'mute_until' => null,
        ]);
    }

    /**
     * Test sending message using receiver_id auto-creates a room or uses an existing one.
     */
    public function test_can_send_message_using_receiver_id_auto_creates_room(): void
    {
        // 1. Send message to user2 (no room exists yet)
        $response = $this->actingAs($this->user1, 'api')
            ->postJson('/api/chat/messages', [
                'receiver_id' => $this->user2->id,
                'message' => 'First message creates room auto',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.message', 'First message creates room auto');

        $roomId = $response->json('data.chat_room_id');
        $this->assertNotNull($roomId);

        // Verify database has room and both participants
        $this->assertDatabaseHas('chat_rooms', [
            'id' => $roomId,
            'type' => ChatRoomTypeEnum::SINGLE->value,
        ]);

        $this->assertDatabaseHas('chat_participants', [
            'chat_room_id' => $roomId,
            'user_id' => $this->user1->id,
        ]);

        $this->assertDatabaseHas('chat_participants', [
            'chat_room_id' => $roomId,
            'user_id' => $this->user2->id,
        ]);

        // 2. Send another message to user2, it should reuse the same room
        $response2 = $this->actingAs($this->user1, 'api')
            ->postJson('/api/chat/messages', [
                'receiver_id' => $this->user2->id,
                'message' => 'Second message in same room',
            ]);

        $response2->assertStatus(201)
            ->assertJsonPath('data.chat_room_id', $roomId)
            ->assertJsonPath('data.message', 'Second message in same room');

        // 3. Test validation error when both receiver_id and chat_room_id are missing
        $response3 = $this->actingAs($this->user1, 'api')
            ->postJson('/api/chat/messages', [
                'message' => 'No target room or receiver',
            ]);

        $response3->assertStatus(422);

        // 4. Test blocked user cannot be messaged directly using receiver_id
        // User 2 blocks User 1
        $this->actingAs($this->user2, 'api')
            ->postJson("/api/chat/block/{$this->user1->id}")
            ->assertOk();

        // User 1 tries to send message to User 2 using receiver_id (should fail with 403)
        $response4 = $this->actingAs($this->user1, 'api')
            ->postJson('/api/chat/messages', [
                'receiver_id' => $this->user2->id,
                'message' => 'Hello blocker',
            ]);

        $response4->assertStatus(403);
    }
}
