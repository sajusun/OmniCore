<x-mail.layout :title="'Support Request Received — ' . config('app.name', 'CruzeHub')" :preheader="'We received your report #' . $support->ticket_no">

    <x-mail.header :icon="null" :title="config('app.name', 'CruzeHub')" :subtitle="'Support Request #' . $support->ticket_no" />

    <x-mail.body>
        <x-mail.text>
            Hello <strong>{{ $user->name ?? 'User' }}</strong>,
        </x-mail.text>

        <x-mail.text>
            Thank you for reaching out to us. We have received your app issue/feedback report and our team is currently reviewing it.
        </x-mail.text>

        <div style="background-color: #f3f4f6; border-left: 4px solid #4f46e5; padding: 16px; margin: 20px 0;">
            <p style="margin: 0 0 8px 0; font-size: 14px; color: #4b5563;"><strong>Ticket No:</strong> #{{ $support->ticket_no }}</p>
            <p style="margin: 0 0 8px 0; font-size: 14px; color: #4b5563;"><strong>Subject:</strong> {{ $support->subject }}</p>
            <p style="margin: 0 0 8px 0; font-size: 14px; color: #4b5563;"><strong>Category:</strong> {{ $support->category?->label() ?? ucfirst($support->category) }}</p>
            <p style="margin: 0; font-size: 14px; color: #4b5563;"><strong>Details:</strong></p>
            <p style="margin: 6px 0 0 0; font-size: 14px; color: #1f2937; white-space: pre-wrap;">{{ $support->message }}</p>
        </div>

        <x-mail.text>
            You will receive a notification and an email as soon as an administrator reviews and provides an update.
        </x-mail.text>
    </x-mail.body>

    <x-mail.footer />

</x-mail.layout>
