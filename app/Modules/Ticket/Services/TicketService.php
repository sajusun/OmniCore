<?php

declare(strict_types=1);

namespace App\Modules\Ticket\Services;

use App\Models\User;
use App\Modules\Ticket\Enums\TicketPriority;
use App\Modules\Ticket\Enums\TicketStatus;
use App\Modules\Ticket\Events\TicketAssignedEvent;
use App\Modules\Ticket\Events\TicketCreatedEvent;
use App\Modules\Ticket\Events\TicketRepliedEvent;
use App\Modules\Ticket\Events\TicketStatusUpdatedEvent;
use App\Modules\Ticket\Models\Ticket;
use App\Modules\Ticket\Models\TicketAttachment;
use App\Modules\Ticket\Models\TicketReply;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketService
{
    public function __construct() {}

    /**
     * Create a new ticket.
     */
    public function createTicket(User $user, array $data, array $attachments = []): Ticket
    {
        return DB::transaction(function () use ($user, $data, $attachments) {
            $priority = $data['priority'] ?? TicketPriority::MEDIUM->value;
            if ($priority instanceof TicketPriority) {
                $priority = $priority->value;
            }

            $ticket = Ticket::create([
                'user_id'     => $user->id,
                'category_id' => $data['category_id'] ?? null,
                'priority'    => $priority,
                'status'      => TicketStatus::OPEN->value,
                'subject'     => $data['subject'],
                'message'     => $data['message'],
            ]);

            foreach ($attachments as $file) {
                if ($file instanceof UploadedFile) {
                    $this->saveAttachment($ticket, $file);
                }
            }

            // Dispatch domain event (Notification listener handles delivery decoupled)
            event(new TicketCreatedEvent($ticket));

            return $ticket->fresh(['category', 'attachments', 'user']);
        });
    }

    /**
     * Reply to a ticket.
     */
    public function replyToTicket(
        Ticket $ticket,
        User $user,
        string $message,
        bool $isInternalNote = false,
        array $attachments = []
    ): TicketReply {
        return DB::transaction(function () use ($ticket, $user, $message, $isInternalNote, $attachments) {
            $reply = TicketReply::create([
                'ticket_id'        => $ticket->id,
                'user_id'          => $user->id,
                'is_internal_note' => $isInternalNote,
                'message'          => $message,
            ]);

            foreach ($attachments as $file) {
                if ($file instanceof UploadedFile) {
                    $this->saveAttachment($reply, $file);
                }
            }

            $ticket->update([
                'last_reply_at' => now(),
            ]);

            if (!$isInternalNote) {
                $isStaff = $user->id !== $ticket->user_id;

                if ($isStaff) {
                    $ticket->update(['status' => TicketStatus::WAITING_ON_USER->value]);
                } else {
                    if (in_array($ticket->status, [TicketStatus::WAITING_ON_USER, TicketStatus::RESOLVED], true)) {
                        $ticket->update(['status' => TicketStatus::OPEN->value]);
                    }
                }

                // Dispatch domain event for reply notifications
                event(new TicketRepliedEvent($ticket, $reply, $isStaff));
            }

            return $reply->fresh(['user', 'attachments']);
        });
    }

    /**
     * Assign ticket to staff member.
     */
    public function assignTicket(Ticket $ticket, ?User $staff): Ticket
    {
        $ticket->update([
            'assigned_to' => $staff?->id,
            'status'      => $staff ? TicketStatus::IN_PROGRESS->value : $ticket->status->value,
        ]);

        if ($staff) {
            // Dispatch domain event for assignment notification
            event(new TicketAssignedEvent($ticket, $staff));
        }

        return $ticket->fresh(['assignee']);
    }

    /**
     * Update ticket status.
     */
    public function updateStatus(Ticket $ticket, TicketStatus|string $status): Ticket
    {
        $statusValue = $status instanceof TicketStatus ? $status->value : $status;

        $updates = ['status' => $statusValue];
        if ($statusValue === TicketStatus::RESOLVED->value) {
            $updates['resolved_at'] = now();
        } elseif ($statusValue === TicketStatus::CLOSED->value) {
            $updates['closed_at'] = now();
        }

        $ticket->update($updates);

        // Dispatch domain event for status updates
        event(new TicketStatusUpdatedEvent($ticket, $statusValue));

        return $ticket->fresh();
    }

    /**
     * Close ticket.
     */
    public function closeTicket(Ticket $ticket, User $user): Ticket
    {
        return $this->updateStatus($ticket, TicketStatus::CLOSED);
    }

    /**
     * Save attachment for ticket or reply.
     */
    public function saveAttachment(Model $attachable, UploadedFile $file): TicketAttachment
    {
        $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('tickets/attachments', $filename, 'public');

        return TicketAttachment::create([
            'attachable_type' => get_class($attachable),
            'attachable_id'   => $attachable->id,
            'file_name'       => $file->getClientOriginalName(),
            'file_path'       => $path,
            'file_size'       => $file->getSize() ?: 0,
            'file_type'       => $file->getMimeType(),
        ]);
    }
}
