{{--
    Verification Link Email — Password Reset
    ────────────────────────────────────────
    View:   emails.link.password-reset
    Vars:   string $verificationUrl — the signed URL the user clicks
            int    $expiryMinutes   — validity window in minutes
--}}
<x-mail.layout
    :title="'Reset Your Password — ' . config('app.name')"
    :preheader="'Click to reset your password. Link expires in ' . $expiryMinutes . ' minute' . ($expiryMinutes !== 1 ? 's' : '') . '.'">

    <x-mail.header
        icon="&#128273;"
        title="Reset Your Password"
        subtitle="Click the button below to set a new password"
    />

    <x-mail.body>

        <x-mail.text>
            We received a request to reset the password associated with your account.
            Click the button below to choose a new password. This link is valid for
            <strong>{{ $expiryMinutes }} minute{{ $expiryMinutes !== 1 ? 's' : '' }}</strong>
            and can only be used once.
        </x-mail.text>

        <x-mail.button
            :url="$verificationUrl"
            label="Reset Password"
        />

        <x-mail.alert type="success">
            &#x23F1; This link expires in
            <strong>{{ $expiryMinutes }} minute{{ $expiryMinutes !== 1 ? 's' : '' }}</strong>.
            After that, you will need to submit a new password reset request.
        </x-mail.alert>

        <x-mail.alert type="warning">
            <strong>&#9888; Didn't request this?</strong>
            If you did not request a password reset, please ignore this email.
            Your account remains secure and your password has not been changed.
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
