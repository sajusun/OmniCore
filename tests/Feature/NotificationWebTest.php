<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Notification\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationWebTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_fetch_notifications_with_cursor_pagination(): void
    {
        // Create 15 notifications
        for ($i = 1; $i <= 15; $i++) {
            Notification::create([
                'user_id' => $this->user->id,
                'title'   => "Notification {$i}",
                'body'    => "Body {$i}",
                'type'    => 'general',
            ]);
        }

        $response = $this->actingAs($this->user)->getJson(route('notification.index', ['per_page' => 10]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'data',
                    'next_cursor',
                    'prev_cursor',
                    'has_more',
                ],
                'next_cursor',
                'has_more',
                'unread_count',
            ])
            ->assertJson([
                'status'       => 'success',
                'has_more'     => true,
                'unread_count' => 15,
            ]);

        $this->assertCount(10, $response->json('data.data'));
        $this->assertNotNull($response->json('next_cursor'));
    }

    public function test_user_can_mark_single_notification_as_read(): void
    {
        $notification = Notification::create([
            'user_id' => $this->user->id,
            'title'   => 'Test Unread',
            'body'    => 'Test Body',
            'type'    => 'general',
        ]);

        $this->assertNull($notification->read_at);

        $response = $this->actingAs($this->user)
            ->postJson(route('notification.read.single', ['id' => $notification->id]));

        $response->assertStatus(200)
            ->assertJson([
                'code'   => 200,
                'status' => 'success',
            ]);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_can_delete_specific_notification(): void
    {
        $notification = Notification::create([
            'user_id' => $this->user->id,
            'title'   => 'To Delete',
            'body'    => 'Delete me',
            'type'    => 'general',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('notification.destroy', ['id' => $notification->id]));

        $response->assertStatus(200)
            ->assertJson([
                'code'   => 200,
                'status' => 'success',
            ]);

        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        Notification::create([
            'user_id' => $this->user->id,
            'title'   => 'Notif 1',
            'body'    => 'Body 1',
            'type'    => 'general',
        ]);
        Notification::create([
            'user_id' => $this->user->id,
            'title'   => 'Notif 2',
            'body'    => 'Body 2',
            'type'    => 'general',
        ]);

        $this->assertEquals(2, $this->user->unreadAppNotifications()->count());

        $response = $this->actingAs($this->user)
            ->postJson(route('notification.read.all'));

        $response->assertStatus(200)
            ->assertJson([
                'code'   => 200,
                'status' => 'success',
            ]);

        $this->assertEquals(0, $this->user->unreadAppNotifications()->count());
    }
}
