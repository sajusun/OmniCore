<x-mail.layout :title="'Support Update — ' . config('app.name', 'CruzeHub')" :preheader="'Admin replied to your report #' . $support->ticket_no">

    <x-mail.header :icon="null" :title="config('app.name', 'CruzeHub')" :subtitle="'Support Update: #' . $support->ticket_no" />

    <x-mail.body>
        <x-mail.text>
            Hello <strong>{{ $user->name ?? 'User' }}</strong>,
        </x-mail.text>

        <x-mail.text>
            An administrator has responded to your support request regarding <strong>"{{ $support->subject }}"</strong>.
        </x-mail.text>

        <!-- Admin Response Box -->
        <div style="background-color: #eef2ff; border: 1px solid #c7d2fe; border-left: 4px solid #6366f1; padding: 18px; border-radius: 8px; margin: 20px 0;">
            <p style="margin: 0 0 10px 0; font-size: 13px; font-weight: bold; color: #4338ca; text-transform: uppercase; letter-spacing: 0.5px;">
                Administrator Response
            </p>
            <div style="font-size: 15px; color: #1e1b4b; line-height: 1.6; white-space: pre-wrap;">
                {{ $reply->message }}
            </div>
            <div style="margin-top: 12px; font-size: 12px; color: #6366f1;">
                Status: <strong>{{ $support->status?->label() ?? ucfirst($support->status) }}</strong>
            </div>
        </div>

        <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; padding: 14px; border-radius: 6px; margin: 16px 0;">
            <p style="margin: 0 0 6px 0; font-size: 12px; color: #6b7280; text-transform: uppercase;">Original Message ({{ $support->ticket_no }}):</p>
            <p style="margin: 0; font-size: 13px; color: #4b5563; font-style: italic;">"{{ \Illuminate\Support\Str::limit($support->message, 180) }}"</p>
        </div>

        <x-mail.text>
            You can also check the updated status directly inside the app.
        </x-mail.text>
    </x-mail.body>

    <x-mail.footer />

</x-mail.layout>
