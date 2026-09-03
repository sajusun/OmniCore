<?php

namespace App\Modules\Chat\Services;

use App\Modules\Chat\Models\ChatParticipant;
use App\Modules\Chat\Models\ChatRoom;
use Carbon\Carbon;

class ChatSettingService
{
    /**
     * Helper to get participant settings.
     */
    protected function getParticipant(ChatRoom $room, int $userId): ChatParticipant
    {
        return ChatParticipant::where([
            'chat_room_id' => $room->id,
            'user_id'      => $userId,
        ])->firstOrFail();
    }

    /**
     * Enable push/in-app notifications for participant.
     */
    public function enableNotification(ChatRoom $room, int $userId): void
    {
        $this->getParticipant($room, $userId)->update(['notification_enabled' => true]);
    }

    /**
     * Disable push/in-app notifications for participant.
     */
    public function disableNotification(ChatRoom $room, int $userId): void
    {
        $this->getParticipant($room, $userId)->update(['notification_enabled' => false]);
    }

    /**
     * Enable sound notifications.
     */
    public function enableSound(ChatRoom $room, int $userId): void
    {
        $this->getParticipant($room, $userId)->update(['sound_enabled' => true]);
    }

    /**
     * Disable sound notifications.
     */
    public function disableSound(ChatRoom $room, int $userId): void
    {
        $this->getParticipant($room, $userId)->update(['sound_enabled' => false]);
    }

    /**
     * Mute the chat room until a specific time.
     */
    public function mute(ChatRoom $room, int $userId, Carbon $until): void
    {
        $this->getParticipant($room, $userId)->update(['mute_until' => $until]);
    }

    /**
     * Unmute the chat room.
     */
    public function unmute(ChatRoom $room, int $userId): void
    {
        $this->getParticipant($room, $userId)->update(['mute_until' => null]);
    }

    /**
     * Check if room is muted for the user.
     */
    public function isMuted(ChatRoom $room, int $userId): bool
    {
        $participant = $this->getParticipant($room, $userId);
        return $participant->mute_until && $participant->mute_until->isFuture();
    }

    /**
     * Check if the user can be notified.
     */
    public function canNotify(ChatRoom $room, int $userId): bool
    {
        $participant = $this->getParticipant($room, $userId);
        return $participant->notification_enabled && !$this->isMuted($room, $userId);
    }

    public function archive(ChatRoom $room, int $userId): void
    {
        $participant = $this->getParticipant($room, $userId);
        $settings = $participant->settings ?? [];
        $settings['archived'] = true;
        $participant->update(['settings' => $settings]);
    }

    public function unarchive(ChatRoom $room, int $userId): void
    {
        $participant = $this->getParticipant($room, $userId);
        $settings = $participant->settings ?? [];
        $settings['archived'] = false;
        $participant->update(['settings' => $settings]);
    }

    public function pin(ChatRoom $room, int $userId): void
    {
        $participant = $this->getParticipant($room, $userId);
        $settings = $participant->settings ?? [];
        $settings['pinned'] = true;
        $participant->update(['settings' => $settings]);
    }

    public function unpin(ChatRoom $room, int $userId): void
    {
        $participant = $this->getParticipant($room, $userId);
        $settings = $participant->settings ?? [];
        $settings['pinned'] = false;
        $participant->update(['settings' => $settings]);
    }

    public function nickname(ChatRoom $room, int $userId, string $nickname): void
    {
        $participant = $this->getParticipant($room, $userId);
        $settings = $participant->settings ?? [];
        $settings['nickname'] = $nickname;
        $participant->update(['settings' => $settings]);
    }

    public function wallpaper(ChatRoom $room, int $userId, string $wallpaper): void
    {
        $participant = $this->getParticipant($room, $userId);
        $settings = $participant->settings ?? [];
        $settings['wallpaper'] = $wallpaper;
        $participant->update(['settings' => $settings]);
    }

    public function autoDelete(ChatRoom $room, int $userId, int $seconds): void
    {
        $participant = $this->getParticipant($room, $userId);
        $settings = $participant->settings ?? [];
        $settings['auto_delete_after'] = $seconds;
        $participant->update(['settings' => $settings]);
    }

    public function messageTranslation(ChatRoom $room, int $userId, string $locale): void
    {
        $participant = $this->getParticipant($room, $userId);
        $settings = $participant->settings ?? [];
        $settings['translation_locale'] = $locale;
        $participant->update(['settings' => $settings]);
    }
}
