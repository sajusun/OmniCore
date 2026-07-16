{{--
    Verification Link Email — Purpose-driven
    ─────────────────────────────────────────
    View:   emails.verification_link
    Vars:   string $purpose          — 'password_reset' | 'email_verification'
            string $verificationUrl  — the signed URL the user clicks
            int    $expiryMinutes    — validity window in minutes
--}}
@php
    $isPwReset = $purpose === 'password_reset';
@endphp

<x-mail.layout
    :title="($isPwReset ? 'Reset Your Password' : 'Confirm Your Email') . ' — ' . config('app.name')"
    :preheader="$isPwReset
        ? 'Click to reset your password. Link expires in ' . $expiryMinutes . ' minute' . ($expiryMinutes !== 1 ? 's' : '') . '.'
        : 'Confirm your email address. Link expires in ' . $expiryMinutes . ' minute' . ($expiryMinutes !== 1 ? 's' : '') . '.'">

    <x-mail.header
        :icon="$isPwReset ? '&#128273;' : '&#9993;'"
        :title="$isPwReset ? 'Reset Your Password' : 'Confirm Your Email Address'"
        :subtitle="$isPwReset
            ? 'Click the button below to set a new password'
            : 'One click to activate your account'"
    />

    <x-mail.body>

        <x-mail.text>
            @if ($isPwReset)
                We received a request to reset the password associated with your
                account. Click the button below to set a new password. This link
                is valid for <strong>{{ $expiryMinutes }} minute{{ $expiryMinutes !== 1 ? 's' : '' }}</strong>
                and can only be used once.
            @else
                Thank you for registering! Please confirm your email address by
                clicking the button below. This link is valid for
                <strong>{{ $expiryMinutes }} minute{{ $expiryMinutes !== 1 ? 's' : '' }}</strong>
                and can only be used once.
            @endif
        </x-mail.text>

        <x-mail.button
            :url="$verificationUrl"
            :label="$isPwReset ? 'Reset Password' : 'Verify Email Address'"
        />

        <x-mail.alert type="success">
            &#x23F1; This link expires in
            <strong>{{ $expiryMinutes }} minute{{ $expiryMinutes !== 1 ? 's' : '' }}</strong>.
            @if ($isPwReset)
                After that, you will need to submit a new password reset request.
            @else
                After that, you will need to request a new verification email.
            @endif
        </x-mail.alert>

        <x-mail.alert type="warning">
            <strong>&#9888; Didn't request this?</strong>
            @if ($isPwReset)
                If you did not request a password reset, please ignore this email.
                Your account remains secure and your password has not been changed.
            @else
                If you did not create an account with us, please ignore this email.
                No account will be activated and you won't hear from us again.
            @endif
        </x-mail.alert>

        <x-mail.text variant="small">
            If the button above doesn't work, copy and paste this link into your browser:
        </x-mail.text>

        <p style="margin: 0 0 20px; word-break: break-all;">
            <a href="{{ $verificationUrl }}"
               style="color: #4f46e5; font-family: Arial, Helvetica, sans-serif;
                      font-size: 12px; text-decoration: underline; word-break: break-all;">
                {{ $verificationUrl }}
            </a>
        </p>

    </x-mail.body>

    <x-mail.footer />

</x-mail.layout>
