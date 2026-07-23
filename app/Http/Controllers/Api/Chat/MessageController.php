<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Services\Chat\MessageService;
use App\Services\Chat\ChatPermissionService;
use App\Http\Requests\Chat\SendMessageRequest;
use App\Http\Resources\Chat\MessageResource;
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
     * List paginated messages of a room.
     */
    public function index(ChatRoom $room, Request $request)
    {
        if (!$this->permissionService->canView(auth('api')->user(), $room)) {
            return $this->error('You do not have permission to view messages in this room.', null, 403);
        }

        $perPage = $request->query('per_page', 15);
        $messages = $this->messageService->messages($room, $perPage);

        return $this->response(
            status: true,
            message: 'Messages retrieved successfully',
            code: 200,
            data: MessageResource::collection($messages),
            paginate: true,
            paginateData: $messages
        );
    }

    /**
     * Send a message.
     */
    public function send(SendMessageRequest $request)
    {
        $sender = auth('api')->user();
        $validated = $request->validated();
        $room = ChatRoom::findOrFail($validated['chat_room_id']);

        if (!$this->permissionService->canSend($sender, $room)) {
            return $this->error('You do not have permission to send messages to this room.', null, 403);
        }

        try {
            $message = $this->messageService->send($room, $sender->id, $validated);

            return $this->success(
                data: new MessageResource($message),
                message: 'Message sent successfully',
                status: 201
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 400);
        }
    }

    /**
     * Update a message.
     */
    public function update(Message $message, Request $request)
    {
        if (!$this->permissionService->canEdit(auth('api')->user(), $message)) {
            return $this->error('You do not have permission to edit this message.', null, 403);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $updatedMessage = $this->messageService->update($message, $validated);

        return $this->success(
            data: new MessageResource($updatedMessage),
            message: 'Message updated successfully'
        );
    }

    /**
     * Delete a message.
     */
    public function destroy(Message $message)
    {
        if (!$this->permissionService->canDelete(auth('api')->user(), $message)) {
            return $this->error('You do not have permission to delete this message.', null, 403);
        }

        $this->messageService->delete($message);

        return $this->success(
            message: 'Message deleted successfully'
        );
    }
}
