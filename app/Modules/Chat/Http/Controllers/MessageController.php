<?php

namespace App\Modules\Chat\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Modules\Chat\Http\Requests\ForwardMessageRequest;
use App\Modules\Chat\Http\Requests\SendMessageRequest;
use App\Modules\Chat\Http\Requests\TypingRequest;
use App\Modules\Chat\Http\Requests\UpdateMessageRequest;
use App\Modules\Chat\Http\Resources\MessageResource;
use App\Modules\Chat\Http\Resources\ReadReceiptResource;
use App\Modules\Chat\Models\ChatRoom;
use App\Modules\Chat\Models\Message;
use App\Modules\Chat\Services\ChatPermissionService;
use App\Modules\Chat\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(
        protected MessageService $messageService,
        protected ChatPermissionService $permissionService
    ) {
        parent::__construct();
    }

    /**
     * Get paginated messages for a room with delta sync support.
     */
    public function index(ChatRoom $room, Request $request): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->permissionService->canView($user, $room)) {
            return Helper::jsonResponse(false, 'You do not have access to this chat room.', 403);
        }

        $perPage = (int) $request->query('per_page', 20);
        $afterId = $request->query('after_id') ? (int) $request->query('after_id') : null;

        $messages = $this->messageService->messages($room, $perPage, $afterId);

        return Helper::jsonResponse(
            true,
            'Messages retrieved successfully',
            200,
            MessageResource::collection($messages),
            [
                'current_page' => $messages->currentPage(),
                'last_page'    => $messages->lastPage(),
                'total'        => $messages->total(),
            ]
        );
    }

    /**
     * Send a new message.
     */
    public function send(SendMessageRequest $request): JsonResponse
    {
        $user = auth('api')->user();
        $roomId = $request->validated('chat_room_id');
        $room = ChatRoom::findOrFail($roomId);

        if (!$this->permissionService->canSend($user, $room)) {
            return Helper::jsonResponse(false, 'You do not have permission to send messages in this room.', 403);
        }

        $message = $this->messageService->send($room, $user->id, $request->validated());

        return Helper::jsonResponse(
            true,
            'Message sent successfully',
            201,
            new MessageResource($message)
        );
    }

    /**
     * Mark messages as read in a room.
     */
    public function markAsRead(ChatRoom $room, Request $request): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->permissionService->canView($user, $room)) {
            return Helper::jsonResponse(false, 'You do not have access to this chat room.', 403);
        }

        $messageId = (int) ($request->input('message_id') ?? $room->messages()->max('id') ?? 0);
        if ($messageId > 0) {
            $this->messageService->markAsRead($room, $user, $messageId);
        }

        return Helper::jsonResponse(true, 'Messages marked as read successfully', 200);
    }

    /**
     * Get read receipts for a message.
     */
    public function readReceipts(Message $message): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->permissionService->canView($user, $message->room)) {
            return Helper::jsonResponse(false, 'You do not have access to this chat room.', 403);
        }

        $receipts = $this->messageService->getReadReceipts($message);

        return Helper::jsonResponse(
            true,
            'Read receipts retrieved successfully',
            200,
            ReadReceiptResource::collection($receipts)
        );
    }

    /**
     * Broadcast user typing presence.
     */
    public function typing(ChatRoom $room, TypingRequest $request): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->permissionService->canView($user, $room)) {
            return Helper::jsonResponse(false, 'You do not have access to this chat room.', 403);
        }

        $this->messageService->broadcastTyping($room, $user, (bool) $request->validated('is_typing'));

        return Helper::jsonResponse(true, 'Typing state broadcasted successfully', 200);
    }

    /**
     * Forward messages to multiple target rooms.
     */
    public function forward(ForwardMessageRequest $request): JsonResponse
    {
        $user = auth('api')->user();
        $validated = $request->validated();

        $forwarded = $this->messageService->forwardMessages(
            $user,
            $validated['message_ids'],
            $validated['target_room_ids']
        );

        return Helper::jsonResponse(
            true,
            'Messages forwarded successfully',
            201,
            MessageResource::collection(collect($forwarded))
        );
    }

    /**
     * Search message text inside a room.
     */
    public function search(ChatRoom $room, Request $request): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->permissionService->canView($user, $room)) {
            return Helper::jsonResponse(false, 'You do not have access to this chat room.', 403);
        }

        $query = (string) $request->query('q', '');
        $messages = $this->messageService->search($room, $query, (int) $request->query('per_page', 20));

        return Helper::jsonResponse(
            true,
            'Search results retrieved successfully',
            200,
            MessageResource::collection($messages),
            [
                'current_page' => $messages->currentPage(),
                'last_page'    => $messages->lastPage(),
                'total'        => $messages->total(),
            ]
        );
    }

    /**
     * Shared media gallery in a room.
     */
    public function sharedMedia(ChatRoom $room, Request $request): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->permissionService->canView($user, $room)) {
            return Helper::jsonResponse(false, 'You do not have access to this chat room.', 403);
        }

        $type = $request->query('type');
        $mediaMessages = $this->messageService->sharedMedia($room, $type, (int) $request->query('per_page', 20));

        return Helper::jsonResponse(
            true,
            'Shared media retrieved successfully',
            200,
            MessageResource::collection($mediaMessages),
            [
                'current_page' => $mediaMessages->currentPage(),
                'last_page'    => $mediaMessages->lastPage(),
                'total'        => $mediaMessages->total(),
            ]
        );
    }

    /**
     * Update a message.
     */
    public function update(Message $message, UpdateMessageRequest $request): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->permissionService->canEdit($user, $message)) {
            return Helper::jsonResponse(false, 'You do not have permission to edit this message.', 403);
        }

        $updated = $this->messageService->update($message, $request->validated());

        return Helper::jsonResponse(
            true,
            'Message updated successfully',
            200,
            new MessageResource($updated)
        );
    }

    /**
     * Delete a message.
     */
    public function destroy(Message $message): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->permissionService->canDelete($user, $message)) {
            return Helper::jsonResponse(false, 'You do not have permission to delete this message.', 403);
        }

        $this->messageService->delete($message);

        return Helper::jsonResponse(true, 'Message deleted successfully', 200);
    }
}
