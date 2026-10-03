<?php

namespace App\Modules\AppSupport\Services;

use App\Models\User;
use App\Modules\AppSupport\Enums\SupportStatus;
use App\Modules\AppSupport\Mail\AppSupportReceivedMail;
use App\Modules\AppSupport\Mail\AppSupportReplyMail;
use App\Modules\AppSupport\Models\AppSupport;
use App\Modules\AppSupport\Models\AppSupportReply;
use App\Modules\AppSupport\Events\AppSupportAdminRepliedEvent;
use App\Modules\AppSupport\Events\AppSupportReportCreatedEvent;
use App\Modules\AppSupport\Events\AppSupportUserRepliedEvent;
use App\Modules\Media\Traits\HandlesMedia;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AppSupportService
{
    use HandlesMedia;

    /**
     * Create a new support report / issue submitted by user.
     */
    public function createReport(User $user, array $data, $attachments = null): AppSupport
    {
        return DB::transaction(function () use ($user, $data, $attachments) {
            // Generate unique ticket number: SUP-YYYYMMDD-XXXX
            $ticketNo = 'SUP-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            $support = AppSupport::create([
                'ticket_no'    => $ticketNo,
                'user_id'      => $user->id,
                'subject'      => $data['subject'],
                'category'     => $data['category'],
                'message'      => $data['message'],
                'device_os'    => $data['device_os'] ?? null,
                'device_model' => $data['device_model'] ?? null,
                'app_version'  => $data['app_version'] ?? null,
                'status'       => SupportStatus::PENDING,
            ]);

            // Save attachments to polymorphic media table
            if (!empty($attachments)) {
                $this->uploadMedia($support, $attachments, 'app_support');
            }

            // Create initial automated system acknowledgment reply
            $systemReply = AppSupportReply::create([
                'app_support_id' => $support->id,
                'sender_type'    => 'system',
                'user_id'        => null,
                'message'        => 'Thank you for reaching out! We have received your report and our team is currently reviewing it. We will notify you once there is an update.',
            ]);

            // Dispatch event for notifications / listeners
            event(new AppSupportReportCreatedEvent($support, $user));

            // Dispatch Confirmation Email to User
            try {
                if (!empty($user->email)) {
                    Mail::to($user->email)->send(new AppSupportReceivedMail($support));
                }
            } catch (Exception $e) {
                Log::error('AppSupport Received Email Error: ' . $e->getMessage());
            }

            return $support->fresh(['media', 'replies']);
        });
    }

    /**
     * Send an admin reply to a support report.
     */
    public function sendAdminReply(AppSupport $support, string $message, User $admin, ?string $newStatus = null, $attachments = null): AppSupportReply
    {
        return DB::transaction(function () use ($support, $message, $admin, $newStatus, $attachments) {
            // Create admin reply record
            $reply = AppSupportReply::create([
                'app_support_id' => $support->id,
                'sender_type'    => 'admin',
                'user_id'        => $admin->id,
                'message'        => $message,
            ]);

            // Upload any reply attachments
            if (!empty($attachments)) {
                $this->uploadMedia($reply, $attachments, 'app_support');
            }

            // Update status (default to 'replied' if not specified)
            $statusToSet = $newStatus ? SupportStatus::from($newStatus) : SupportStatus::REPLIED;
            $support->update(['status' => $statusToSet]);

            // Dispatch event for notifications / listeners
            event(new AppSupportAdminRepliedEvent($support, $reply, $admin));

            // Send Email to User
            try {
                if (!empty($support->user?->email)) {
                    Mail::to($support->user->email)->send(new AppSupportReplyMail($support, $reply));
                }
            } catch (Exception $e) {
                Log::error('AppSupport Admin Reply Email Error: ' . $e->getMessage());
            }

            return $reply->fresh(['media', 'author']);
        });
    }

    /**
     * Send a user follow-up reply to an existing support report.
     */
    public function sendUserReply(AppSupport $support, string $message, User $user, $attachments = null): AppSupportReply
    {
        return DB::transaction(function () use ($support, $message, $user, $attachments) {
            // Create user reply record
            $reply = AppSupportReply::create([
                'app_support_id' => $support->id,
                'sender_type'    => 'user',
                'user_id'        => $user->id,
                'message'        => $message,
            ]);

            // Upload any reply attachments
            if (!empty($attachments)) {
                $this->uploadMedia($reply, $attachments, 'app_support');
            }

            // Update status back to 'in_progress' or 'pending' if it was resolved/closed
            if (in_array($support->status?->value ?? $support->status, ['resolved', 'closed', 'replied'])) {
                $support->update(['status' => SupportStatus::IN_PROGRESS]);
            }

            // Dispatch event for notifications / listeners
            event(new AppSupportUserRepliedEvent($support, $reply, $user));

            return $reply->fresh(['media', 'author']);
        });
    }

    /**
     * Change status of a support report (e.g. resolve or close).
     */
    public function updateStatus(AppSupport $support, string $status): bool
    {
        $statusEnum = SupportStatus::from($status);
        return $support->update(['status' => $statusEnum]);
    }

    /**
     * Get paginated reports for Admin with search and filters.
     */
    public function getAdminReports(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return AppSupport::query()
            ->with(['user', 'media', 'latestReply'])
            ->status($filters['status'] ?? null)
            ->category($filters['category'] ?? null)
            ->search($filters['search'] ?? null)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get user's own reports with pagination.
     */
    public function getUserReports(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return AppSupport::query()
            ->with(['media', 'replies.media', 'replies.author'])
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get full details of a specific report.
     */
    public function getReportDetail(int|string $id, ?int $userId = null): AppSupport
    {
        $query = AppSupport::query()
            ->with([
                'user',
                'media',
                'replies' => fn($q) => $q->with(['media', 'author'])->orderBy('created_at', 'asc'),
            ]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->findOrFail($id);
    }
}
