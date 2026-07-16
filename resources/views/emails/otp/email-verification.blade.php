{{--
    OTP Email — Email Verification
    ──────────────────────────────
    View:   emails.otp.email-verification
    Vars:   string $otp            — the 4–8 digit code
            int    $expiryMinutes  — validity window in minutes
--}}
<x-mail.layout
    :title="'Verify Your Email — ' . config('app.name')"
    :preheader="'Your verification code is: ' . $otp . '. Expires in ' . $expiryMinutes . ' minute' . ($expiryMinutes !== 1 ? 's' : '') . '.'">

    <x-mail.header
        icon="&#9993;"
        title="Verify Your Email Address"
        subtitle="Use the one-time code below to activate your account"
    />

    <x-mail.body>

        <x-mail.text>
            Thank you for signing up! To activate your account, please enter the
            one-time code below. This code is only valid for
            <strong>{{ $expiryMinutes }} minute{{ $expiryMinutes !== 1 ? 's' : '' }}</strong>.
        </x-mail.text>

        <x-mail.otp-box
            :code="$otp"
            :expiry-minutes="$expiryMinutes"
            label="Email Verification Code"
        />

        <x-mail.alert type="warning">
            <strong>&#9888; Never share this code.</strong>
            Our team will never ask for your OTP via email, phone, or any other channel.
        </x-mail.alert>

        <x-mail.text variant="small">
            If you did not create an account with us, you can safely ignore this email.
            No action is required and your inbox will not receive further messages.
        </x-mail.text>

    </x-mail.body>

    <x-mail.footer />

</x-mail.layout>
