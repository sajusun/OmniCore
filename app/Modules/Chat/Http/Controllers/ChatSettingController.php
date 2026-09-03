<?php

namespace App\Modules\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Chat\Http\Requests\ChatSettingRequest;
use App\Modules\Chat\Models\ChatRoom;
use App\Modules\Chat\Services\ChatPermissionService;
use App\Modules\Chat\Services\ChatSettingService;
use Carbon\Carbon;

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
    public function updateNotification(ChatRoom $room, ChatSettingRequest $request)
    {
        $this->checkMembership($room);
        $userId = auth('api')->id();
        $enabled = $request->validated('enabled', true);

        if ($enabled) {
            $this->settingService->enableNotification($room, $userId);
        } else {
            $this->settingService->disableNotification($room, $userId);
        }

        return $this->success(
            message: 'Notification settings updated successfully'
        );
    }

    /**
     * Update sound settings.
     */
    public function updateSound(ChatRoom $room, ChatSettingRequest $request)
    {
        $this->checkMembership($room);
        $userId = auth('api')->id();
        $enabled = $request->validated('enabled', true);

        if ($enabled) {
            $this->settingService->enableSound($room, $userId);
        } else {
            $this->settingService->disableSound($room, $userId);
        }

        return $this->success(
            message: 'Sound settings updated successfully'
        );
    }

    /**
     * Mute the chat room.
     */
    public function mute(ChatRoom $room, ChatSettingRequest $request)
    {
        $this->checkMembership($room);
        $userId = auth('api')->id();
        $untilInput = $request->validated('mute_until');

        if (!$untilInput) {
            return $this->error('The mute_until field is required for muting.', null, 422);
        }

        $until = Carbon::parse($untilInput);
        $this->settingService->mute($room, $userId, $until);

        return $this->success(
            message: 'Chat room muted successfully'
        );
    }

    /**
     * Unmute the chat room.
     */
    public function unmute(ChatRoom $room)
    {
        $this->checkMembership($room);
        $userId = auth('api')->id();
        $this->settingService->unmute($room, $userId);

        return $this->success(
            message: 'Chat room unmuted successfully'
        );
    }
}
