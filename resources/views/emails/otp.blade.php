{{--
    OTP Email — Purpose-driven
    ──────────────────────────
    View:   emails.otp
    Vars:   string $purpose        — 'password_reset' | 'email_verification'
            string $otp            — the 4–8 digit code
            int    $expiryMinutes  — validity window in minutes
--}}
@php
    $isPwReset = $purpose === 'password_reset';
@endphp

<x-mail.layout
    :title="($isPwReset ? 'Password Reset OTP' : 'Email Verification OTP') . ' — ' . config('app.name')"
    :preheader="'Your ' . ($isPwReset ? 'password reset' : 'verification') . ' code is: ' . $otp . '. Expires in ' . $expiryMinutes . ' minute' . ($expiryMinutes !== 1 ? 's' : '') . '.'">

    <x-mail.header
        :icon="$isPwReset ? '&#128273;' : '&#9993;'"
        :title="$isPwReset ? 'Password Reset Request' : 'Verify Your Email Address'"
        :subtitle="$isPwReset
            ? 'Use the code below to reset your password'
            : 'Use the code below to activate your account'"
    />

    <x-mail.body>

        <x-mail.text>
            @if ($isPwReset)
                We received a request to reset your password. Enter the one-time
                code below to proceed. If you did not make this request,
                you can safely ignore this email.
            @else
                Thank you for signing up! To activate your account, please enter
                the one-time code below. If you did not create an account,
                you can safely ignore this email.
            @endif
        </x-mail.text>

        <x-mail.otp-box
            :code="$otp"
            :expiry-minutes="$expiryMinutes"
            :label="$isPwReset ? 'Password Reset Code' : 'Email Verification Code'"
        />

        <x-mail.alert type="warning">
            <strong>&#9888; Never share this code.</strong>
            Our team will never ask for your OTP via email, phone, or any other channel.
        </x-mail.alert>

        <x-mail.text variant="small">
            @if ($isPwReset)
                If you did not request a password reset, please ignore this email.
                Your account remains secure and no changes have been made.
            @else
                If you did not create an account with us, please ignore this email.
                No action is required and you will not hear from us again.
            @endif
        </x-mail.text>

    </x-mail.body>

    <x-mail.footer />

</x-mail.layout>
