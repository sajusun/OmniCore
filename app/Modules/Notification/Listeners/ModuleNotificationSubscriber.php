<?php

declare(strict_types=1);

namespace App\Modules\Notification\Listeners;

use App\Models\User;
use App\Modules\Notification\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ModuleNotificationSubscriber implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The name of the connection the job should be sent to.
     */
    public ?string $queue = 'default';

    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handleTicketCreated(object $event): void
    {
        try {
            $ticket = $event->ticket;
            $user = $event->user;

            $admins = User::whereHas('roles', fn($q) => $q->whereIn('name', ['super_admin', 'admin', 'Super Admin', 'Admin', 'agent']))->get();
            if ($admins->isNotEmpty()) {
                $this->notificationService->sendMany(
                    $admins,
                    title: "New Support Ticket: #{$ticket->ticket_number}",
                    body: "A new support ticket has been opened by {$user->name}: {$ticket->subject}",
                    type: 'ticket',
                    referenceType: 'ticket',
                    referenceId: $ticket->id,
                    meta: ['ticket_id' => $ticket->id, 'ticket_number' => $ticket->ticket_number]
                );
            }
        } catch (Throwable $e) {
            Log::error('ModuleNotificationSubscriber TicketCreated error: ' . $e->getMessage());
        }
    }

    public function handleTicketReplied(object $event): void
    {
        try {
            $ticket = $event->ticket;
            $message = $event->message;
            $sender = $event->user;

            $recipient = ($sender->id === $ticket->user_id)
                ? $ticket->assignedAgent
                : $ticket->user;

            if ($recipient) {
                $this->notificationService->send(
                    $recipient,
                    title: "New Reply on Ticket: #{$ticket->ticket_number}",
                    body: "{$sender->name}: " . Str::limit($message->message, 80),
                    type: 'ticket_reply',
                    referenceType: 'ticket',
                    referenceId: $ticket->id,
                    meta: ['ticket_id' => $ticket->id, 'message_id' => $message->id]
                );
            }
        } catch (Throwable $e) {
            Log::error('ModuleNotificationSubscriber TicketReplied error: ' . $e->getMessage());
        }
    }

    public function handleTicketAssigned(object $event): void
    {
        try {
            $ticket = $event->ticket;
            $assignedTo = $event->assignedTo;
            $assignedBy = $event->assignedBy;

            $this->notificationService->send(
                $assignedTo,
                title: "Ticket Assigned: #{$ticket->ticket_number}",
                body: "{$assignedBy->name} assigned ticket '{$ticket->subject}' to you.",
                type: 'ticket_assigned',
                referenceType: 'ticket',
                referenceId: $ticket->id,
                meta: ['ticket_id' => $ticket->id]
            );
        } catch (Throwable $e) {
            Log::error('ModuleNotificationSubscriber TicketAssigned error: ' . $e->getMessage());
        }
    }

    public function handleTicketStatusUpdated(object $event): void
    {
        try {
            $ticket = $event->ticket;
            $status = $event->status;

            if ($ticket->user) {
                $this->notificationService->send(
                    $ticket->user,
                    title: "Ticket Status Updated: #{$ticket->ticket_number}",
                    body: "Your ticket '{$ticket->subject}' status has been updated to: {$status}.",
                    type: 'ticket_status',
                    referenceType: 'ticket',
                    referenceId: $ticket->id,
                    meta: ['ticket_id' => $ticket->id, 'status' => $status]
                );
            }
        } catch (Throwable $e) {
            Log::error('ModuleNotificationSubscriber TicketStatusUpdated error: ' . $e->getMessage());
        }
    }

    public function handleReviewSubmitted(object $event): void
    {
        try {
            $review = $event->review;
            $user = $event->user;

            $admins = User::whereHas('roles', fn($q) => $q->whereIn('name', ['super_admin', 'admin', 'Super Admin', 'Admin']))->get();
            if ($admins->isNotEmpty()) {
                $this->notificationService->sendMany(
                    $admins,
                    title: "New Review Submitted ({$review->rating}/5 stars)",
                    body: "{$user->name} posted a review: " . Str::limit((string) $review->comment, 80),
                    type: 'review',
                    referenceType: 'review',
                    referenceId: $review->id,
                    meta: ['review_id' => $review->id, 'rating' => $review->rating]
                );
            }
        } catch (Throwable $e) {
            Log::error('ModuleNotificationSubscriber ReviewSubmitted error: ' . $e->getMessage());
        }
    }

    public function handleReviewReplied(object $event): void
    {
        try {
            $review = $event->review;
            $reply = $event->reply;

            if ($review->user) {
                $this->notificationService->send(
                    $review->user,
                    title: 'New Reply on Your Review',
                    body: Str::limit((string) $reply->comment, 80),
                    type: 'review_reply',
                    referenceType: 'review',
                    referenceId: $review->id,
                    meta: ['review_id' => $review->id, 'reply_id' => $reply->id]
                );
            }
        } catch (Throwable $e) {
            Log::error('ModuleNotificationSubscriber ReviewReplied error: ' . $e->getMessage());
        }
    }

    public function handleVendorStoreRegistered(object $event): void
    {
        try {
            $store = $event->store;
            $owner = $event->owner;

            $admins = User::whereHas('roles', fn($q) => $q->whereIn('name', ['super_admin', 'admin', 'Super Admin', 'Admin']))->get();
            if ($admins->isNotEmpty()) {
                $this->notificationService->sendMany(
                    $admins,
                    title: "New Vendor Store Registered: {$store->name}",
                    body: "User {$owner->name} created store {$store->name}.",
                    type: 'vendor',
                    referenceType: 'vendor',
                    referenceId: $store->id,
                    meta: ['store_id' => $store->id]
                );
            }
        } catch (Throwable $e) {
            Log::error('ModuleNotificationSubscriber VendorStoreRegistered error: ' . $e->getMessage());
        }
    }

    public function handleVendorSaleRecorded(object $event): void
    {
        try {
            $store = $event->store;
            $orderTotal = $event->orderTotal;
            $vendorShare = $event->vendorShare;

            if ($store->owner) {
                $this->notificationService->send(
                    $store->owner,
                    title: "Sale recorded for {$store->name}",
                    body: "Order of $" . number_format($orderTotal, 2) . " processed. Your share of $" . number_format($vendorShare, 2) . " has been credited to store balance.",
                    type: 'vendor_sale',
                    referenceType: 'vendor',
                    referenceId: $store->id,
                    meta: ['store_id' => $store->id, 'vendor_share' => $vendorShare]
                );
            }
        } catch (Throwable $e) {
            Log::error('ModuleNotificationSubscriber VendorSaleRecorded error: ' . $e->getMessage());
        }
    }

    public function handleVendorPayoutRequested(object $event): void
    {
        try {
            $store = $event->store;
            $payout = $event->payout;
            $isWallet = $event->isWallet;

            if ($store->owner) {
                $this->notificationService->send(
                    $store->owner,
                    title: "Vendor Payout: $" . number_format((float) $payout->amount, 2),
                    body: $isWallet ? "Payout successfully deposited directly into your Wallet." : "Payout request submitted for admin review.",
                    type: 'vendor_payout',
                    referenceType: 'vendor_payout',
                    referenceId: $payout->id,
                    meta: ['payout_id' => $payout->id, 'amount' => $payout->amount]
                );
            }
        } catch (Throwable $e) {
            Log::error('ModuleNotificationSubscriber VendorPayoutRequested error: ' . $e->getMessage());
        }
    }

    public function handleVendorStatusUpdated(object $event): void
    {
        try {
            $store = $event->store;
            $status = $event->status;

            if ($store->owner) {
                $this->notificationService->send(
                    $store->owner,
                    title: "Store status updated: {$store->name}",
                    body: "Your store status is now: " . ucfirst((string) $status),
                    type: 'vendor_status',
                    referenceType: 'vendor',
                    referenceId: $store->id,
                    meta: ['store_id' => $store->id, 'status' => $status]
                );
            }
        } catch (Throwable $e) {
            Log::error('ModuleNotificationSubscriber VendorStatusUpdated error: ' . $e->getMessage());
        }
    }

    public function handleAffiliateCommissionEarned(object $event): void
    {
        try {
            $account = $event->account;
            $commission = $event->commission;
            $orderAmount = $event->orderAmount;
            $commissionAmount = $event->commissionAmount;

            if ($account->user) {
                $this->notificationService->send(
                    $account->user,
                    title: "Affiliate Commission Earned! ($" . number_format($commissionAmount, 2) . ")",
                    body: "A referred user completed an order of $" . number_format($orderAmount, 2) . ". Your commission has been credited.",
                    type: 'affiliate_commission',
                    referenceType: 'commission',
                    referenceId: $commission->id,
                    meta: ['commission_id' => $commission->id, 'amount' => $commissionAmount]
                );
            }
        } catch (Throwable $e) {
            Log::error('ModuleNotificationSubscriber AffiliateCommissionEarned error: ' . $e->getMessage());
        }
    }

    public function handleAffiliatePayoutProcessed(object $event): void
    {
        try {
            $account = $event->account;
            $amount = $event->amount;

            if ($account->user) {
                $this->notificationService->send(
                    $account->user,
                    title: "Affiliate Payout Processed",
                    body: "$" . number_format($amount, 2) . " has been deposited directly into your Wallet balance.",
                    type: 'affiliate_payout',
                    referenceType: 'affiliate',
                    referenceId: $account->id,
                    meta: ['payout_amount' => $amount]
                );
            }
        } catch (Throwable $e) {
            Log::error('ModuleNotificationSubscriber AffiliatePayoutProcessed error: ' . $e->getMessage());
        }
    }

    public function handleAppSupportReportCreated(object $event): void
    {
        try {
            $support = $event->support;
            $user = $event->user;

            $this->notificationService->send(
                user: $user,
                title: 'Support Request Received',
                body: "Your support request (#{$support->ticket_no}) has been received by our team.",
                type: 'app_support',
                referenceType: 'AppSupport',
                referenceId: $support->id,
                action: 'OPEN_SUPPORT_REPORT',
                meta: [
                    'ticket_no' => $support->ticket_no,
                    'category'  => is_object($support->category) ? $support->category->value : $support->category,
                ]
            );

            $adminUsers = User::whereHas('roles', fn($q) => $q->whereIn('name', ['super_admin', 'admin', 'Super Admin', 'Admin']))->get();
            if ($adminUsers->isNotEmpty()) {
                $this->notificationService->sendMany(
                    users: $adminUsers,
                    title: 'New Support Request Submitted',
                    body: "User {$user->name} submitted a support report (#{$support->ticket_no}): {$support->subject}",
                    type: 'app_support_admin',
                    referenceType: 'AppSupport',
                    referenceId: $support->id,
                    action: 'VIEW_ADMIN_SUPPORT_REPORT',
                    meta: [
                        'ticket_no' => $support->ticket_no,
                        'user_id'   => $user->id,
                    ]
                );
            }
        } catch (Throwable $e) {
            Log::error('ModuleNotificationSubscriber AppSupportReportCreated error: ' . $e->getMessage());
        }
    }

    public function handleAppSupportAdminReplied(object $event): void
    {
        try {
            $support = $event->support;
            $reply = $event->reply;

            $this->notificationService->send(
                user: $support->user_id,
                title: 'Support Request Update',
                body: "Admin replied to your report (#{$support->ticket_no}): " . Str::limit($reply->message, 80),
                type: 'app_support',
                referenceType: 'AppSupport',
                referenceId: $support->id,
                action: 'OPEN_SUPPORT_REPORT',
                meta: [
                    'ticket_no' => $support->ticket_no,
                    'reply_id'  => $reply->id,
                    'status'    => is_object($support->status) ? $support->status->value : $support->status,
                ]
            );
        } catch (Throwable $e) {
            Log::error('ModuleNotificationSubscriber AppSupportAdminReplied error: ' . $e->getMessage());
        }
    }

    public function handleAppSupportUserReplied(object $event): void
    {
        try {
            $support = $event->support;
            $reply = $event->reply;
            $user = $event->user;

            $adminUsers = User::whereHas('roles', fn($q) => $q->whereIn('name', ['super_admin', 'admin', 'Super Admin', 'Admin']))->get();
            if ($adminUsers->isNotEmpty()) {
                $this->notificationService->sendMany(
                    users: $adminUsers,
                    title: 'User Replied to Support Request',
                    body: "User {$user->name} replied to report (#{$support->ticket_no}): " . Str::limit($reply->message, 80),
                    type: 'app_support_admin',
                    referenceType: 'AppSupport',
                    referenceId: $support->id,
                    action: 'VIEW_ADMIN_SUPPORT_REPORT',
                    meta: [
                        'ticket_no' => $support->ticket_no,
                        'reply_id'  => $reply->id,
                        'user_id'   => $user->id,
                    ]
                );
            }
        } catch (Throwable $e) {
            Log::error('ModuleNotificationSubscriber AppSupportUserReplied error: ' . $e->getMessage());
        }
    }

    /**
     * Register listeners for subscriber.
     *
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        $map = [
            'App\Modules\Ticket\Events\TicketCreatedEvent'                 => 'handleTicketCreated',
            'App\Modules\Ticket\Events\TicketRepliedEvent'                 => 'handleTicketReplied',
            'App\Modules\Ticket\Events\TicketAssignedEvent'                => 'handleTicketAssigned',
            'App\Modules\Ticket\Events\TicketStatusUpdatedEvent'           => 'handleTicketStatusUpdated',

            'App\Modules\Review\Events\ReviewSubmittedEvent'               => 'handleReviewSubmitted',
            'App\Modules\Review\Events\ReviewRepliedEvent'                 => 'handleReviewReplied',

            'App\Modules\Vendor\Events\VendorStoreRegisteredEvent'         => 'handleVendorStoreRegistered',
            'App\Modules\Vendor\Events\VendorSaleRecordedEvent'            => 'handleVendorSaleRecorded',
            'App\Modules\Vendor\Events\VendorPayoutRequestedEvent'         => 'handleVendorPayoutRequested',
            'App\Modules\Vendor\Events\VendorStatusUpdatedEvent'           => 'handleVendorStatusUpdated',

            'App\Modules\Affiliate\Events\AffiliateCommissionEarnedEvent' => 'handleAffiliateCommissionEarned',
            'App\Modules\Affiliate\Events\AffiliatePayoutProcessedEvent'  => 'handleAffiliatePayoutProcessed',

            'App\Modules\AppSupport\Events\AppSupportReportCreatedEvent'   => 'handleAppSupportReportCreated',
            'App\Modules\AppSupport\Events\AppSupportAdminRepliedEvent'    => 'handleAppSupportAdminReplied',
            'App\Modules\AppSupport\Events\AppSupportUserRepliedEvent'     => 'handleAppSupportUserReplied',
        ];

        $listeners = [];
        foreach ($map as $eventClass => $handler) {
            if (class_exists($eventClass)) {
                $listeners[$eventClass] = $handler;
            }
        }

        return $listeners;
    }
}
