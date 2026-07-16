{{--
    Verification Link Email — Email Verification
    ────────────────────────────────────────────
    View:   emails.link.email-verification
    Vars:   string $verificationUrl — the signed URL the user clicks
            int    $expiryMinutes   — validity window in minutes
--}}
<x-mail.layout
    :title="'Confirm Your Email — ' . config('app.name')"
    :preheader="'Confirm your email address. Link expires in ' . $expiryMinutes . ' minute' . ($expiryMinutes !== 1 ? 's' : '') . '.'">

    <x-mail.header
        icon="&#9993;"
        title="Confirm Your Email Address"
        subtitle="One click to activate your account"
    />

    <x-mail.body>

        <x-mail.text>
            Thank you for registering! Please confirm your email address by clicking
            the button below. This link is valid for
            <strong>{{ $expiryMinutes }} minute{{ $expiryMinutes !== 1 ? 's' : '' }}</strong>
            and can only be used once.
        </x-mail.text>

        <x-mail.button
            :url="$verificationUrl"
            label="Verify Email Address"
        />

        <x-mail.alert type="success">
            &#x23F1; This link expires in
            <strong>{{ $expiryMinutes }} minute{{ $expiryMinutes !== 1 ? 's' : '' }}</strong>.
            After that, you will need to request a new verification email.
        </x-mail.alert>

        <x-mail.alert type="warning">
            <strong>&#9888; Didn't create an account?</strong>
            If you did not register with us, please ignore this email.
            No account will be activated and you won't hear from us again.
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
