{{--
Welcome Email — New User Registration
──────────────────────────────────────
View: emails.welcome
Vars: string $name — the user's display name
string $loginUrl — URL to the login / dashboard page
--}}
<x-mail.layout :title="'Welcome to ' . config('app.name') . '!'"
    :preheader="'Hi ' . $name . ', your account is active. Get started now.'">

    <x-mail.header icon="&#127881;" title="Welcome to {{ config('app.name') }}!"
        subtitle="Your account is active and ready to go" />

    <x-mail.body>

        {{-- Personal greeting --}}
        <x-mail.text>
            Hi <strong>{{ $name }}</strong>, welcome aboard!
        </x-mail.text>

        <x-mail.text>
            We're delighted to have you join <strong>{{ config('app.name') }}</strong>.
            Your account has been successfully created and is ready to use.
            Click the button below to log in and get started.
        </x-mail.text>

        {{-- Primary CTA --}}
        <x-mail.button :url="$loginUrl" label="Go to Dashboard" />

        {{-- Spacer --}}
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td height="8" style="font-size: 0; line-height: 0; mso-line-height-rule: exactly;">&nbsp;</td>
            </tr>
        </table>

        {{-- What's next --}}
        <x-mail.text variant="heading">
            What you can do next
        </x-mail.text>

        {{-- Step list using a table for cross-client compatibility --}}
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 24px;">

            <tr>
                <td valign="top" width="32" style="padding: 0 12px 14px 0; font-family: Arial, Helvetica, sans-serif;
                           font-size: 18px; line-height: 1; color: #4f46e5;">
                    &#9312;
                </td>
                <td valign="top" style="padding-bottom: 14px; font-family: Arial, Helvetica, sans-serif;
                           font-size: 14px; color: #374151; line-height: 1.6;">
                    <strong style="color: #111827;">Complete your profile</strong><br>
                    Add your details so we can personalise your experience.
                </td>
            </tr>

            <tr>
                <td valign="top" width="32" style="padding: 0 12px 14px 0; font-family: Arial, Helvetica, sans-serif;
                           font-size: 18px; line-height: 1; color: #4f46e5;">
                    &#9313;
                </td>
                <td valign="top" style="padding-bottom: 14px; font-family: Arial, Helvetica, sans-serif;
                           font-size: 14px; color: #374151; line-height: 1.6;">
                    <strong style="color: #111827;">Explore the dashboard</strong><br>
                    Get familiar with all the tools and features available to you.
                </td>
            </tr>

            <tr>
                <td valign="top" width="32" style="padding: 0 12px 0 0; font-family: Arial, Helvetica, sans-serif;
                           font-size: 18px; line-height: 1; color: #4f46e5;">
                    &#9314;
                </td>
                <td valign="top" style="font-family: Arial, Helvetica, sans-serif;
                           font-size: 14px; color: #374151; line-height: 1.6;">
                    <strong style="color: #111827;">Invite your team</strong><br>
                    Collaborate with colleagues by inviting them to your workspace.
                </td>
            </tr>

        </table>

        {{-- Support note --}}
        <x-mail.alert type="info">
            &#128172; Have questions? Our support team is always happy to help.
            Simply reply to this email or visit our help centre.
        </x-mail.alert>

        <x-mail.text variant="muted">
            If you did not create this account, please contact us immediately
            so we can secure it. We take security very seriously.
        </x-mail.text>

    </x-mail.body>

    <x-mail.footer />

</x-mail.layout>