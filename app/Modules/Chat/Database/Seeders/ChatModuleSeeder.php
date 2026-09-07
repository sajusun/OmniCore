<?php

namespace App\Modules\Chat\Database\Seeders;

use App\Models\User;
use App\Modules\Chat\Enums\ChatRoomTypeEnum;
use App\Modules\Chat\Enums\ParticipantRoleEnum;
use App\Modules\Chat\Models\ChatParticipant;
use App\Modules\Chat\Models\ChatRoom;
use App\Modules\Chat\Models\Message;
use Illuminate\Database\Seeder;

class ChatModuleSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::take(4)->get();

        if ($users->count() < 2) {
            return;
        }

        $user1 = $users[0];
        $user2 = $users[1];
        $user3 = $users[2] ?? $users[0];

        // 1. Create Direct Single Room
        $singleRoom = ChatRoom::create([
            'type'       => ChatRoomTypeEnum::SINGLE->value,
            'created_by' => $user1->id,
        ]);

        ChatParticipant::create([
            'chat_room_id' => $singleRoom->id,
            'user_id'      => $user1->id,
            'role'         => ParticipantRoleEnum::OWNER->value,
        ]);

        ChatParticipant::create([
            'chat_room_id' => $singleRoom->id,
            'user_id'      => $user2->id,
            'role'         => ParticipantRoleEnum::MEMBER->value,
        ]);

        Message::create([
            'chat_room_id' => $singleRoom->id,
            'sender_id'    => $user1->id,
            'message_type' => 'text',
            'message'      => 'Hey! Welcome to the OmniCore platform.',
        ]);

        Message::create([
            'chat_room_id' => $singleRoom->id,
            'sender_id'    => $user2->id,
            'message_type' => 'text',
            'message'      => 'Thanks! The modular architecture and API performance look incredible.',
        ]);

        // 2. Create Group Room
        $groupRoom = ChatRoom::create([
            'type'        => ChatRoomTypeEnum::GROUP->value,
            'name'        => 'Engineering Team',
            'description' => 'Core development and architecture discussions',
            'created_by'  => $user1->id,
        ]);

        foreach ([$user1, $user2, $user3] as $index => $u) {
            ChatParticipant::create([
                'chat_room_id' => $groupRoom->id,
                'user_id'      => $u->id,
                'role'         => $index === 0 ? ParticipantRoleEnum::OWNER->value : ParticipantRoleEnum::MEMBER->value,
            ]);
        }

        Message::create([
            'chat_room_id' => $groupRoom->id,
            'sender_id'    => $user1->id,
            'message_type' => 'text',
            'message'      => 'Welcome team to the Engineering Hub!',
        ]);

        echo "✓ Chat Module Seeding Complete!\n";
    }
}
