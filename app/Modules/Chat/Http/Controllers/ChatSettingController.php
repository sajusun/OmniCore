<?php

namespace App\Modules\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Chat\Enums\MuteDurationEnum;
use App\Modules\Chat\Http\Requests\ChatSettingRequest;
use App\Modules\Chat\Http\Requests\MuteRoomRequest;
use App\Modules\Chat\Models\ChatRoom;
use App\Modules\Chat\Services\ChatPermissionService;
use App\Modules\Chat\Services\ChatSettingService;
use Illuminate\Http\JsonResponse;

class ChatSettingController extends Controller
{
    public function __construct(
        protected ChatSettingService $settingService,
        protected ChatPermissionService $permissionService
    ) {
        parent::__construct();
    }

    /**
     * Helper to verify room membership.
     */
    protected function checkMembership(ChatRoom $room)
    {
        if (!$this->permissionService->canView(auth('api')->user(), $room)) {
            abort(403, 'You are not a participant in this chat room.');
        }
    }

    /**
     * Update notification settings.
     */
    public function updateNotification(ChatRoom $room, ChatSettingRequest $request): JsonResponse
    {
        $this->checkMembership($room);
        $userId = auth('api')->id();
        $enabled = $request->validated('enabled', true);

        if ($enabled) {
            $this->settingService->enableNotification($room, $userId);
        } else {
            $this->settingService->disableNotification($room, $userId);
        }

        return $this->success(null, 'Notification settings updated successfully');
    }

    /**
     * Update sound settings.
     */
    public function updateSound(ChatRoom $room, ChatSettingRequest $request): JsonResponse
    {
        $this->checkMembership($room);
        $userId = auth('api')->id();
        $enabled = $request->validated('enabled', true);

        if ($enabled) {
            $this->settingService->enableSound($room, $userId);
        } else {
            $this->settingService->disableSound($room, $userId);
        }

        return $this->success(null, 'Sound settings updated successfully');
    }

    /**
     * Mute the chat room with Telegram-style presets.
     */
    public function mute(ChatRoom $room, MuteRoomRequest $request): JsonResponse
    {
        $this->checkMembership($room);
        $userId = auth('api')->id();
        $duration = MuteDurationEnum::from($request->validated('duration'));
        $customTime = $request->validated('mute_until');

        $until = $this->settingService->mute($room, $userId, $duration, $customTime);

        return $this->success(
            ['mute_until' => $until->toIso8601String()],
            'Chat room muted successfully'
        );
    }

    /**
     * Unmute the chat room.
     */
    public function unmute(ChatRoom $room): JsonResponse
    {
        $this->checkMembership($room);
        $userId = auth('api')->id();
        $this->settingService->unmute($room, $userId);

        return $this->success(null, 'Chat room unmuted successfully');
    }
}
