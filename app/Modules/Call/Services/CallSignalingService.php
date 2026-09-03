<?php

namespace App\Modules\Call\Services;

use App\Models\User;
use App\Modules\Call\Enums\CallStatusEnum;
use App\Modules\Call\Enums\CallTypeEnum;
use App\Modules\Call\Enums\ParticipantCallStatusEnum;
use App\Modules\Call\Events\CallAccepted;
use App\Modules\Call\Events\CallBusy;
use App\Modules\Call\Events\CallEnded;
use App\Modules\Call\Events\CallInitiated;
use App\Modules\Call\Events\CallRejected;
use App\Modules\Call\Events\CallRinging;
use App\Modules\Call\Events\IceCandidateExchanged;
use App\Modules\Call\Events\SdpOfferAnswerExchanged;
use App\Modules\Call\Events\TrackStateChanged;
use App\Modules\Call\Models\CallParticipant;
use App\Modules\Call\Models\CallSession;
use App\Modules\Chat\Enums\MessageTypeEnum;
use App\Modules\Chat\Models\ChatRoom;
use App\Modules\Chat\Models\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CallSignalingService
{
    /**
     * Initiate a 1-to-1 or group call.
     */
    public function initiateCall(User $caller, array $data): CallSession
    {
        $type = CallTypeEnum::from($data['type']);
        $chatRoomId = $data['chat_room_id'] ?? null;

        // Gather participant IDs
        $participantIds = $data['participant_ids'] ?? [];
        if (!empty($data['receiver_id'])) {
            $participantIds[] = (int) $data['receiver_id'];
        }
        $participantIds = array_unique(array_filter($participantIds, fn($id) => (int) $id !== $caller->id));

        if (empty($participantIds)) {
            throw ValidationException::withMessages([
                'receiver_id' => 'At least one valid participant is required to initiate a call.',
            ]);
        }

        // Check if caller or any target has blocked the other
        foreach ($participantIds as $targetId) {
            $targetUser = User::find($targetId);
            if ($targetUser && (method_exists($caller, 'hasBlocked') && ($caller->hasBlocked($targetUser) || $caller->isBlockedBy($targetUser)))) {
                throw ValidationException::withMessages([
                    'receiver_id' => "Cannot initiate call with {$targetUser->name} due to privacy blocks.",
                ]);
            }
        }

        return DB::transaction(function () use ($caller, $type, $chatRoomId, $participantIds) {
            $session = CallSession::create([
                'caller_id'    => $caller->id,
                'chat_room_id' => $chatRoomId,
                'type'         => $type,
                'status'       => CallStatusEnum::INITIATING,
            ]);

            // Add caller participant
            CallParticipant::create([
                'call_session_id' => $session->id,
                'user_id'         => $caller->id,
                'status'          => ParticipantCallStatusEnum::CALLING,
                'joined_at'       => now(),
            ]);

            // Add receivers and broadcast incoming call event
            foreach ($participantIds as $targetId) {
                CallParticipant::create([
                    'call_session_id' => $session->id,
                    'user_id'         => $targetId,
                    'status'          => ParticipantCallStatusEnum::CALLING,
                ]);

                try {
                    broadcast(new CallInitiated($session->load('caller'), $targetId))->toOthers();
                } catch (\Throwable $th) {
                    Log::error('CallInitiated broadcast error: ' . $th->getMessage());
                }
            }

            return $session->load(['caller', 'participants.user']);
        });
    }

    /**
     * Receiver acknowledges ringing.
     */
    public function ring(CallSession $session, User $user): void
    {
        $participant = $session->participants()->where('user_id', $user->id)->first();
        if ($participant && $session->status === CallStatusEnum::INITIATING) {
            $session->update(['status' => CallStatusEnum::RINGING]);
            $participant->update(['status' => ParticipantCallStatusEnum::RINGING]);

            try {
                broadcast(new CallRinging($session->uuid, $user->id))->toOthers();
            } catch (\Throwable $th) {
                Log::error('CallRinging broadcast error: ' . $th->getMessage());
            }
        }
    }

    /**
     * Receiver accepts the call.
     */
    public function accept(CallSession $session, User $user): CallSession
    {
        if (in_array($session->status, [CallStatusEnum::ENDED, CallStatusEnum::REJECTED, CallStatusEnum::MISSED])) {
            throw ValidationException::withMessages([
                'call' => 'This call has already ended.',
            ]);
        }

        DB::transaction(function () use ($session, $user) {
            $now = now();
            $session->update([
                'status'     => CallStatusEnum::CONNECTED,
                'started_at' => $session->started_at ?? $now,
            ]);

            $session->participants()->where('user_id', $user->id)->update([
                'status'    => ParticipantCallStatusEnum::CONNECTED,
                'joined_at' => $now,
            ]);

            try {
                broadcast(new CallAccepted($session->uuid, $user->id, $session->channel_name))->toOthers();
            } catch (\Throwable $th) {
                Log::error('CallAccepted broadcast error: ' . $th->getMessage());
            }
        });

        return $session->load(['caller', 'participants.user']);
    }

    /**
     * Receiver declines / rejects the call.
     */
    public function reject(CallSession $session, User $user, ?string $reason = null): void
    {
        DB::transaction(function () use ($session, $user, $reason) {
            $session->participants()->where('user_id', $user->id)->update([
                'status'  => ParticipantCallStatusEnum::REJECTED,
                'left_at' => now(),
            ]);

            // If 1-on-1, mark whole session as rejected/missed
            if ($session->participants()->count() <= 2) {
                $status = ($session->status === CallStatusEnum::INITIATING) ? CallStatusEnum::MISSED : CallStatusEnum::REJECTED;
                $session->update([
                    'status'   => $status,
                    'ended_at' => now(),
                ]);
            }

            try {
                broadcast(new CallRejected($session->uuid, $user->id, $reason))->toOthers();
            } catch (\Throwable $th) {
                Log::error('CallRejected broadcast error: ' . $th->getMessage());
            }
        });
    }

    /**
     * User ends / hangs up the call.
     */
    public function end(CallSession $session, User $user): CallSession
    {
        return DB::transaction(function () use ($session, $user) {
            $now = now();
            $startedAt = $session->started_at ?? $now;
            $duration = $session->status === CallStatusEnum::CONNECTED ? $now->diffInSeconds($startedAt) : 0;

            $session->update([
                'status'   => CallStatusEnum::ENDED,
                'ended_at' => $now,
                'duration' => $duration,
            ]);

            // Update caller & participants left_at and duration
            $session->participants()->whereNull('left_at')->update([
                'left_at'  => $now,
                'status'   => ParticipantCallStatusEnum::LEFT,
                'duration' => $duration,
            ]);

            try {
                broadcast(new CallEnded($session->uuid, $user->id, $duration))->toOthers();
            } catch (\Throwable $th) {
                Log::error('CallEnded broadcast error: ' . $th->getMessage());
            }

            // Push call log message to chat room if associated
            if ($session->chat_room_id && class_exists(Message::class)) {
                $icon = $session->type === CallTypeEnum::VIDEO ? '📹 Video call' : '📞 Audio call';
                $durationFormatted = gmdate($duration >= 3600 ? "H:i:s" : "i:s", $duration);
                $statusText = $duration > 0 ? "ended ({$durationFormatted})" : ($session->status === CallStatusEnum::MISSED ? "missed" : "cancelled");

                Message::create([
                    'chat_room_id' => $session->chat_room_id,
                    'sender_id'    => $session->caller_id,
                    'message_type' => MessageTypeEnum::SYSTEM->value,
                    'message'      => "{$icon} {$statusText}",
                ]);
            }

            return $session->load(['caller', 'participants.user']);
        });
    }

    /**
     * Report busy state.
     */
    public function busy(CallSession $session, User $user): void
    {
        $session->participants()->where('user_id', $user->id)->update([
            'status'  => ParticipantCallStatusEnum::BUSY,
            'left_at' => now(),
        ]);

        if ($session->participants()->count() <= 2) {
            $session->update([
                'status'   => CallStatusEnum::BUSY,
                'ended_at' => now(),
            ]);
        }

        try {
            broadcast(new CallBusy($session->uuid, $user->id))->toOthers();
        } catch (\Throwable $th) {
            Log::error('CallBusy broadcast error: ' . $th->getMessage());
        }
    }

    /**
     * WebRTC SDP & ICE Candidate Signaling Relay.
     */
    public function signal(CallSession $session, User $user, string $type, array $payload, ?int $targetId = null): void
    {
        try {
            if ($type === 'ice_candidate') {
                broadcast(new IceCandidateExchanged($session->uuid, $user->id, $payload))->toOthers();
            } elseif (in_array($type, ['offer', 'answer'])) {
                broadcast(new SdpOfferAnswerExchanged($session->uuid, $user->id, $type, $payload))->toOthers();
            }
        } catch (\Throwable $th) {
            Log::error('Call signaling broadcast error: ' . $th->getMessage());
        }
    }

    /**
     * Track state toggle (mic, camera, screen share).
     */
    public function trackState(CallSession $session, User $user, bool $isMuted, bool $isVideoEnabled, bool $isScreenSharing): void
    {
        $session->participants()->where('user_id', $user->id)->update([
            'is_muted'          => $isMuted,
            'is_video_enabled'  => $isVideoEnabled,
            'is_screen_sharing' => $isScreenSharing,
        ]);

        try {
            broadcast(new TrackStateChanged($session->uuid, $user->id, $isMuted, $isVideoEnabled, $isScreenSharing))->toOthers();
        } catch (\Throwable $th) {
            Log::error('TrackStateChanged broadcast error: ' . $th->getMessage());
        }
    }
}
