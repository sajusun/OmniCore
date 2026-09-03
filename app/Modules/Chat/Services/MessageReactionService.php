<?php

namespace App\Modules\Chat\Services;

use App\Models\User;
use App\Modules\Chat\Events\MessageReactionUpdated;
use App\Modules\Chat\Models\Message;
use App\Modules\Chat\Models\MessageReaction;
use Illuminate\Support\Facades\Log;

class MessageReactionService
{
    /**
     * Toggle or update a reaction on a message.
     */
    public function toggleReaction(Message $message, User $user, string $reaction): array
    {
        $existing = MessageReaction::where('message_id', $message->id)
            ->where('user_id', $user->id)
            ->first();

        $currentReaction = null;

        if ($existing) {
            if ($existing->reaction === $reaction) {
                // Remove reaction if clicking the same one again
                $existing->delete();
                $currentReaction = null;
            } else {
                // Update to new reaction
                $existing->update(['reaction' => $reaction]);
                $currentReaction = $reaction;
            }
        } else {
            // Create new reaction
            MessageReaction::create([
                'message_id' => $message->id,
                'user_id'    => $user->id,
                'reaction'   => $reaction,
            ]);
            $currentReaction = $reaction;
        }

        $summary = $message->reactions()
            ->selectRaw('reaction, count(*) as count')
            ->groupBy('reaction')
            ->pluck('count', 'reaction')
            ->toArray();

        try {
            broadcast(new MessageReactionUpdated(
                $message->chat_room_id,
                $message->id,
                $user->id,
                $currentReaction,
                $summary
            ))->toOthers();
        } catch (\Throwable $th) {
            Log::error('Reaction broadcast error: ' . $th->getMessage());
        }

        return [
            'my_reaction'       => $currentReaction,
            'reactions_summary' => $summary,
        ];
    }

    /**
     * Get all users who reacted to a message.
     */
    public function getReactions(Message $message)
    {
        return $message->reactions()->with('user')->latest()->get();
    }
}
