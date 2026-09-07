<?php

declare(strict_types=1);

namespace App\Modules\Ticket\Services;

use App\Models\User;
use App\Modules\Notification\Services\NotificationService;
use App\Modules\Ticket\Enums\TicketPriority;
use App\Modules\Ticket\Enums\TicketStatus;
use App\Modules\Ticket\Models\Ticket;
use App\Modules\Ticket\Models\TicketAttachment;
use App\Modules\Ticket\Models\TicketReply;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

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

            // Notify admins
            $admins = User::whereHas('roles', fn($q) => $q->whereIn('name', ['super_admin', 'admin', 'Super Admin', 'Admin']))->get();
            if ($admins->isNotEmpty()) {
                $this->notificationService->sendMany(
                    $admins,
                    title: "New Support Ticket #{$ticket->ticket_number}",
                    body: "{$user->name}: {$ticket->subject}",
                    type: 'ticket',
                    referenceType: 'ticket',
                    referenceId: $ticket->id,
                    meta: ['ticket_id' => $ticket->id, 'ticket_number' => $ticket->ticket_number, 'type' => 'ticket_created']
                );
            }

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
                    // Staff replied -> wait for user response
                    $ticket->update(['status' => TicketStatus::WAITING_ON_USER->value]);

                    // Send notification to customer
                    $this->notificationService->send(
                        $ticket->user,
                        title: "Update on Ticket #{$ticket->ticket_number}",
                        body: "Support staff replied: " . Str::limit($message, 80),
                        type: 'ticket',
                        referenceType: 'ticket',
                        referenceId: $ticket->id,
                        meta: ['ticket_id' => $ticket->id, 'ticket_number' => $ticket->ticket_number, 'type' => 'ticket_reply']
                    );
                } else {
                    // Customer replied -> reopen/mark open
                    if (in_array($ticket->status, [TicketStatus::WAITING_ON_USER, TicketStatus::RESOLVED], true)) {
                        $ticket->update(['status' => TicketStatus::OPEN->value]);
                    }

                    // Notify assigned staff or super admins
                    $recipient = $ticket->assignee ?? User::whereHas('roles', fn($q) => $q->whereIn('name', ['super_admin', 'admin', 'Super Admin', 'Admin']))->first();
                    if ($recipient) {
                        $this->notificationService->send(
                            $recipient,
                            title: "New reply on Ticket #{$ticket->ticket_number}",
                            body: "{$user->name}: " . Str::limit($message, 80),
                            type: 'ticket',
                            referenceType: 'ticket',
                            referenceId: $ticket->id,
                            meta: ['ticket_id' => $ticket->id, 'ticket_number' => $ticket->ticket_number, 'type' => 'ticket_user_reply']
                        );
                    }
                }
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
            $this->notificationService->send(
                $staff,
                title: "Ticket Assigned: #{$ticket->ticket_number}",
                body: "You have been assigned ticket: {$ticket->subject}",
                type: 'ticket',
                referenceType: 'ticket',
                referenceId: $ticket->id,
                meta: ['ticket_id' => $ticket->id, 'ticket_number' => $ticket->ticket_number, 'type' => 'ticket_assigned']
            );
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

        // Notify ticket owner
        $this->notificationService->send(
            $ticket->user,
            title: "Ticket #{$ticket->ticket_number} status updated",
            body: "Your ticket status is now: " . ucfirst(str_replace('_', ' ', $statusValue)),
            type: 'ticket',
            referenceType: 'ticket',
            referenceId: $ticket->id,
            meta: ['ticket_id' => $ticket->id, 'ticket_number' => $ticket->ticket_number, 'status' => $statusValue, 'type' => 'ticket_status_change']
        );

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
