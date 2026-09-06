<?php

namespace App\Modules\Notification\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Notification\Mail\AdminCustomMail;
use App\Modules\Notification\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class BulkNotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {
        parent::__construct();
    }

    /**
     * Display the Mail & Notification management page.
     */
    public function index(): View
    {
        $users = User::select('id', 'name', 'email', 'avatar')
            ->where('id', '!=', auth()->id())
            ->latest()
            ->get();

        $viewName = view()->exists('notification::backend.index')
            ? 'notification::backend.index'
            : (view()->exists('bulk_notification::backend.index') ? 'bulk_notification::backend.index' : 'backend.notifications.index');

        return view($viewName, compact('users'));
    }

    /**
     * Send In-App Notification (Push + Database)
     */
    public function sendInApp(Request $request)
    {
        $request->validate([
            'target_type' => 'required|in:all,selected',
            'user_ids'    => 'required_if:target_type,selected|array',
            'user_ids.*'  => 'exists:users,id',
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
            'link'        => 'nullable|string|max:255',
        ]);

        if ($request->target_type === 'all') {
            $users = User::where('id', '!=', auth()->id())->get();
        } else {
            $users = User::whereIn('id', $request->user_ids ?? [])->get();
        }

        if ($users->isEmpty()) {
            return redirect()->back()->with('t-error', 'No users selected to receive notifications.');
        }

        foreach ($users as $user) {
            $this->notificationService->send(
                user: $user,
                title: $request->title,
                body: $request->body,
                type: 'general',
                link: $request->link
            );
        }

        return redirect()->back()->with('t-success', 'In-App notification sent successfully to ' . $users->count() . ' user(s).');
    }

    /**
     * Send Email Notification
     */
    public function sendEmail(Request $request)
    {
        $messageContent = $request->message ?? $request->custom_message;

        $request->validate([
            'recipient_type' => 'required|in:registered,custom',
            'user_ids'       => 'required_if:recipient_type,registered|array',
            'user_ids.*'     => 'exists:users,id',
            'custom_emails'  => 'required_if:recipient_type,custom|string',
            'subject'        => 'required|string|max:255',
        ]);

        if (empty($messageContent)) {
            return redirect()->back()->with('t-error', 'Email message content cannot be empty.');
        }

        if (strip_tags($messageContent) === $messageContent) {
            $messageContent = nl2br(e($messageContent));
        }

        $sentCount = 0;

        if ($request->recipient_type === 'registered') {
            if ($request->has('select_all_registered') && $request->select_all_registered == '1') {
                $users = User::where('id', '!=', auth()->id())->get();
            } else {
                $users = User::whereIn('id', $request->user_ids ?? [])->get();
            }

            if ($users->isEmpty()) {
                return redirect()->back()->with('t-error', 'No registered users selected for sending email.');
            }

            foreach ($users as $user) {
                if ($user->email) {
                    try {
                        Mail::to($user->email)->send(new AdminCustomMail($request->subject, $messageContent, $user->name));
                        $sentCount++;
                    } catch (\Throwable $th) {
                        Log::error('Failed to send email to ' . $user->email . ': ' . $th->getMessage());
                    }
                }
            }
        } else {
            // Custom email list
            $emails = array_filter(array_map('trim', preg_split('/[\s,]+/', $request->custom_emails)));

            if (empty($emails)) {
                return redirect()->back()->with('t-error', 'Please provide at least one valid email address.');
            }

            foreach ($emails as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    try {
                        Mail::to($email)->send(new AdminCustomMail($request->subject, $messageContent));
                        $sentCount++;
                    } catch (\Throwable $th) {
                        Log::error('Failed to send email to ' . $email . ': ' . $th->getMessage());
                    }
                }
            }
        }

        return redirect()->back()->with('t-success', 'Email successfully sent to ' . $sentCount . ' recipient(s).');
    }
}
