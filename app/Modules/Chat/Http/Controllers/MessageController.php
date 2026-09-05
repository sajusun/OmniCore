<?php

namespace App\Modules\Chat\Http\Controllers;

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
use App\Modules\Chat\Services\ChatRoomService;
use App\Modules\Chat\Services\MessageService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(
        protected MessageService $messageService,
        protected ChatPermissionService $permissionService,
        protected ChatRoomService $chatRoomService
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
            return $this->forbidden('You do not have access to this chat room.');
        }

        $perPage = (int) $request->query('per_page', 20);
        $afterId = $request->query('after_id') ? (int) $request->query('after_id') : null;

        $messages = $this->messageService->messages($room, $perPage, $afterId);

        return $this->paginated(
            $messages,
            MessageResource::class,
            'Messages retrieved successfully'
        );
    }

    /**
     * Send a new message.
     */
    public function send(SendMessageRequest $request): JsonResponse
    {
        $user = auth('api')->user();
        $roomId = $request->validated('chat_room_id');
        $receiverId = $request->validated('receiver_id');

        try {
            if ($roomId) {
                $room = ChatRoom::findOrFail($roomId);
            } elseif ($receiverId) {
                $room = $this->chatRoomService->createSingleRoom($user->id, (int) $receiverId);
            } else {
                return $this->error('Target chat room or receiver is required.', null, 422);
            }
        } catch (Exception $e) {
            return $this->error($e->getMessage(), null, 403);
        }

        if (!$this->permissionService->canSend($user, $room)) {
            return $this->forbidden('You do not have permission to send messages in this room.');
        }

        $message = $this->messageService->send($room, $user->id, $request->validated());

        return $this->created(
            new MessageResource($message),
            'Message sent successfully'
        );
    }

    /**
     * Mark messages as read in a room.
     */
    public function markAsRead(ChatRoom $room, Request $request): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->permissionService->canView($user, $room)) {
            return $this->forbidden('You do not have access to this chat room.');
        }

        $messageId = (int) ($request->input('message_id') ?? $room->messages()->max('id') ?? 0);
        if ($messageId > 0) {
            $this->messageService->markAsRead($room, $user, $messageId);
        }

        return $this->success(null, 'Messages marked as read successfully');
    }

    /**
     * Get read receipts for a message.
     */
    public function readReceipts(Message $message): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->permissionService->canView($user, $message->room)) {
            return $this->forbidden('You do not have access to this chat room.');
        }

        $receipts = $this->messageService->getReadReceipts($message);

        return $this->success(
            ReadReceiptResource::collection($receipts),
            'Read receipts retrieved successfully'
        );
    }

    /**
     * Broadcast user typing presence.
     */
    public function typing(ChatRoom $room, TypingRequest $request): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->permissionService->canView($user, $room)) {
            return $this->forbidden('You do not have access to this chat room.');
        }

        $this->messageService->broadcastTyping($room, $user, (bool) $request->validated('is_typing'));

        return $this->success(null, 'Typing state broadcasted successfully');
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

        return $this->created(
            MessageResource::collection(collect($forwarded)),
            'Messages forwarded successfully'
        );
    }

    /**
     * Search message text inside a room.
     */
    public function search(ChatRoom $room, Request $request): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->permissionService->canView($user, $room)) {
            return $this->forbidden('You do not have access to this chat room.');
        }

        $query = (string) $request->query('q', '');
        $messages = $this->messageService->search($room, $query, (int) $request->query('per_page', 20));

        return $this->paginated(
            $messages,
            MessageResource::class,
            'Search results retrieved successfully'
        );
    }

    /**
     * Shared media gallery in a room.
     */
    public function sharedMedia(ChatRoom $room, Request $request): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->permissionService->canView($user, $room)) {
            return $this->forbidden('You do not have access to this chat room.');
        }

        $type = $request->query('type');
        $mediaMessages = $this->messageService->sharedMedia($room, $type, (int) $request->query('per_page', 20));

        return $this->paginated(
            $mediaMessages,
            MessageResource::class,
            'Shared media retrieved successfully'
        );
    }

    /**
     * Update a message.
     */
    public function update(Message $message, UpdateMessageRequest $request): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->permissionService->canEdit($user, $message)) {
            return $this->forbidden('You do not have permission to edit this message.');
        }

        $updated = $this->messageService->update($message, $request->validated());

        return $this->success(
            new MessageResource($updated),
            'Message updated successfully'
        );
    }

    /**
     * Delete a message.
     */
    public function destroy(Message $message): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->permissionService->canDelete($user, $message)) {
            return $this->forbidden('You do not have permission to delete this message.');
        }

        $this->messageService->delete($message);

        return $this->success(null, 'Message deleted successfully');
    }
}
