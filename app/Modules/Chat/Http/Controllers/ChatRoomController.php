<?php

namespace App\Modules\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Chat\Enums\ChatRoomTypeEnum;
use App\Modules\Chat\Http\Requests\CreateChannelRequest;
use App\Modules\Chat\Http\Requests\CreateGroupRequest;
use App\Modules\Chat\Http\Requests\CreateSingleRoomRequest;
use App\Modules\Chat\Http\Resources\ChatRoomResource;
use App\Modules\Chat\Models\ChatRoom;
use App\Modules\Chat\Services\ChatPermissionService;
use App\Modules\Chat\Services\ChatRoomService;
use Illuminate\Http\Request;

class ChatRoomController extends Controller
{
    public function __construct(
        protected ChatRoomService $roomService,
        protected ChatPermissionService $permissionService
    ) {
        parent::__construct();
    }

    /**
     * List user chat rooms.
     */
    public function index(Request $request)
    {
        $userId = auth('api')->id();
        $type = $request->query('type');
        $rooms = $this->roomService->rooms($userId, $type);

        return $this->response(
            status: true,
            message: 'Chat rooms retrieved successfully',
            code: 200,
            data: $rooms,
            paginate: true
        );
    }

    /**
     * Show a chat room.
     */
    public function show(ChatRoom $room)
    {
        if (!$this->permissionService->canView(auth('api')->user(), $room)) {
            return $this->error('You do not have permission to view this chat room.', null, 403);
        }

        $room->load(['creator', 'users']);

        return $this->success(
            data: new ChatRoomResource($room),
            message: 'Chat room details retrieved successfully'
        );
    }

    /**
     * Create a single chat room.
     */
    public function single(CreateSingleRoomRequest $request)
    {
        $creator = auth('api')->user();
        $targetUserId = $request->validated('user_id');

        if (!$this->permissionService->canCreateRoom($creator, ChatRoomTypeEnum::SINGLE, [$targetUserId])) {
            return $this->error('Cannot create chat room: User is blocked.', null, 403);
        }

        try {
            $room = $this->roomService->createSingleRoom($creator->id, $targetUserId);
            $room->load(['creator', 'users']);

            return $this->success(
                data: new ChatRoomResource($room),
                message: 'Single chat room created successfully',
                status: 201
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 400);
        }
    }

    /**
     * Create a group chat room.
     */
    public function group(CreateGroupRequest $request)
    {
        $creator = auth('api')->user();
        $validated = $request->validated();

        if (!$this->permissionService->canCreateRoom($creator, ChatRoomTypeEnum::GROUP, $validated['participant_ids'])) {
            return $this->error('You do not have permission to create this group chat.', null, 403);
        }

        $room = $this->roomService->createGroup(
            $creator->id,
            $validated['name'],
            $validated['participant_ids'],
            $validated['description'] ?? null,
            $validated['image'] ?? null
        );

        $room->load(['creator', 'users']);

        return $this->success(
            data: new ChatRoomResource($room),
            message: 'Group chat room created successfully',
            status: 201
        );
    }

    /**
     * Create a broadcast/announcement channel.
     */
    public function channel(CreateChannelRequest $request)
    {
        $creator = auth('api')->user();
        $validated = $request->validated();

        if (!$this->permissionService->canCreateRoom($creator, ChatRoomTypeEnum::CHANNEL)) {
            return $this->error('You do not have permission to create this channel.', null, 403);
        }

        $room = $this->roomService->createChannel(
            $creator->id,
            $validated['name'],
            $validated['description'] ?? null,
            $validated['image'] ?? null
        );

        $room->load(['creator', 'users']);

        return $this->success(
            data: new ChatRoomResource($room),
            message: 'Channel created successfully',
            status: 201
        );
    }

    /**
     * Update room details.
     */
    public function update(ChatRoom $room, Request $request)
    {
        if (!$this->permissionService->canEdit(auth('api')->user(), $room)) {
            return $this->error('You do not have permission to edit this room.', null, 403);
        }

        $validated = $request->validate([
            'name'        => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image'       => 'nullable|string|max:500',
        ]);

        $this->roomService->updateRoom($room, $validated);
        $room->load(['creator', 'users']);

        return $this->success(
            data: new ChatRoomResource($room),
            message: 'Chat room details updated successfully'
        );
    }

    /**
     * Delete room.
     */
    public function destroy(ChatRoom $room)
    {
        if (!$this->permissionService->canDelete(auth('api')->user(), $room)) {
            return $this->error('You do not have permission to delete this chat room.', null, 403);
        }

        $this->roomService->deleteRoom($room);

        return $this->success(
            message: 'Chat room deleted successfully'
        );
    }

    /**
     * Add participants.
     */
    public function addParticipants(ChatRoom $room, Request $request)
    {
        if (!$this->permissionService->canEdit(auth('api')->user(), $room)) {
            return $this->error('You do not have permission to add participants to this room.', null, 403);
        }

        $validated = $request->validate([
            'user_ids'   => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        try {
            $this->roomService->addParticipants($room, $validated['user_ids']);
            return $this->success(message: 'Participants added successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 400);
        }
    }

    /**
     * Remove participant.
     */
    public function removeParticipant(ChatRoom $room, User $user)
    {
        if (!$this->permissionService->canEdit(auth('api')->user(), $room)) {
            return $this->error('You do not have permission to remove participants from this room.', null, 403);
        }

        try {
            $this->roomService->removeParticipants($room, [$user->id]);
            return $this->success(message: 'Participant removed successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 400);
        }
    }

    /**
     * Leave a chat room.
     */
    public function leave(ChatRoom $room)
    {
        $user = auth('api')->user();
        if (!$this->permissionService->canLeave($user, $room)) {
            return $this->error('You cannot leave this room.', null, 403);
        }

        $this->roomService->leaveRoom($room, $user->id);

        return $this->success(message: 'You have left the chat room successfully');
    }

    /**
     * Join a broadcast channel.
     */
    public function join(ChatRoom $room)
    {
        $user = auth('api')->user();
        if (!$this->permissionService->canJoin($user, $room)) {
            return $this->error('You do not have permission to join this channel.', null, 403);
        }

        try {
            $this->roomService->joinChannel($room, $user->id);
            return $this->success(message: 'You have joined the channel successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 400);
        }
    }

    /**
     * Get total unread messages count across all chat rooms for badge counter.
     */
    public function unreadCount()
    {
        $userId = auth('api')->id();
        $totalUnread = $this->roomService->getTotalUnreadCount($userId);

        return $this->success(
            data: ['total_unread' => $totalUnread],
            message: 'Unread count retrieved successfully'
        );
    }
}
