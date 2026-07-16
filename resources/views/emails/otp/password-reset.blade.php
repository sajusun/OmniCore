{{--
    OTP Email — Password Reset
    ─────────────────────────
    View:   emails.otp.password-reset
    Vars:   string $otp            — the 4–8 digit code
            int    $expiryMinutes  — validity window in minutes
--}}
<x-mail.layout
    :title="'Password Reset OTP — ' . config('app.name')"
    :preheader="'Your password reset code is: ' . $otp . '. Expires in ' . $expiryMinutes . ' minute' . ($expiryMinutes !== 1 ? 's' : '') . '.'">

    <x-mail.header
        icon="&#128273;"
        title="Password Reset Request"
        subtitle="Use the one-time code below to reset your password"
    />

    <x-mail.body>

        <x-mail.text>
            We received a request to reset the password for your account.
            Enter the one-time code below to proceed. This code is only
            valid for <strong>{{ $expiryMinutes }} minute{{ $expiryMinutes !== 1 ? 's' : '' }}</strong>.
        </x-mail.text>

        <x-mail.otp-box
            :code="$otp"
            :expiry-minutes="$expiryMinutes"
            label="Password Reset Code"
        />

        <x-mail.alert type="warning">
            <strong>&#9888; Never share this code.</strong>
            Our team will never ask for your OTP via email, phone, or any other channel.
        </x-mail.alert>

        <x-mail.text variant="small">
            If you did not request a password reset, you can safely ignore this email.
            Your account remains secure and no changes have been made.
        </x-mail.text>

    </x-mail.body>

    <x-mail.footer />

</x-mail.layout>
