<?php

namespace App\Modules\Call\Services;

use App\Models\User;
use App\Modules\Call\Enums\CallStatusEnum;
use App\Modules\Call\Models\CallSession;
use App\Services\BaseService;

class CallLogService extends BaseService
{
    /**
     * Get paginated call history for a user.
     */
    public function history(User $user, ?string $type = null, ?string $status = null)
    {
        $query = CallSession::query()
            ->where(function ($q) use ($user) {
                $q->where('caller_id', $user->id)
                    ->orWhereHas('participants', fn($sub) => $sub->where('user_id', $user->id));
            })
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($status, fn($q) => $q->where('status', $status))
            ->with(['caller', 'participants.user'])
            ->latest();

        return $this->applyPagination($query);
    }

    /**
     * Get missed calls for a user.
     */
    public function missed(User $user)
    {
        $query = CallSession::query()
            ->where('caller_id', '!=', $user->id)
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->whereIn('status', ['calling', 'ringing', 'rejected']);
            })
            ->whereIn('status', [CallStatusEnum::MISSED->value, CallStatusEnum::REJECTED->value, CallStatusEnum::INITIATING->value])
            ->with(['caller', 'participants.user'])
            ->latest();

        return $this->applyPagination($query);
    }

    /**
     * Get details of a single call session.
     */
    public function show(CallSession $session): CallSession
    {
        return $session->load(['caller', 'participants.user', 'chatRoom']);
    }

    /**
     * Delete call log from user's view.
     */
    public function delete(CallSession $session, User $user): void
    {
        // If caller, can delete session or remove participant
        if ($session->caller_id === $user->id) {
            $session->delete();
        } else {
            $session->participants()->where('user_id', $user->id)->delete();
        }
    }
}
