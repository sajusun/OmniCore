<x-mail.layout :title="$subjectText . ' — ' . config('app.name', 'CruzeHub')" :preheader="$subjectText">

    <x-mail.header :icon="null" :title="config('app.name', 'CruzeHub')" :subtitle="$subjectText" />

    <x-mail.body>

        @if (!empty($recipientName))
        <x-mail.text>
            Hello <strong>{{ $recipientName }}</strong>,
        </x-mail.text>
        @endif

        <div class="custom-mail-content"
            style="margin-top: 16px; margin-bottom: 24px; color: #374151; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 1.7;">
            {!! $messageBody !!}
        </div>

    </x-mail.body>

    <x-mail.footer />

</x-mail.layout>
