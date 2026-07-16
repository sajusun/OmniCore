@props([
    'appName' => null,
])

@php
    $name = $appName ?? config('app.name', 'App');
@endphp

{{--
    ROW-LEVEL COMPONENT: renders two <tr> elements (divider + footer text).
    Must be a direct child of the email card <table>.
--}}

{{-- DIVIDER --}}
<tr>
    <td style="padding: 0 40px;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td style="border-top: 1px solid #dee2e6;
                           font-size: 0;
                           line-height: 0;
                           mso-line-height-rule: exactly;">&nbsp;</td>
            </tr>
        </table>
    </td>
</tr>

{{-- FOOTER CONTENT --}}
<tr>
    <td class="email-footer-cell" align="center"
        style="padding: 24px 40px 32px; text-align: center;">

        <p style="margin: 0 0 6px;
                  color: #6c757d;
                  font-family: Arial, Helvetica, sans-serif;
                  font-size: 12px;
                  line-height: 1.6;
                  mso-line-height-rule: exactly;">
            This is an automated message from
            <strong style="color: #495057;">{{ $name }}</strong>.
            Please do not reply to this email.
        </p>

        <p style="margin: 0;
                  color: #adb5bd;
                  font-family: Arial, Helvetica, sans-serif;
                  font-size: 11px;
                  line-height: 1.5;
                  mso-line-height-rule: exactly;">
            &copy; {{ date('Y') }} {{ $name }}. All rights reserved.
        </p>

        @if ($slot->isNotEmpty())
        <div style="margin-top: 14px;
                    color: #6c757d;
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 12px;
                    line-height: 1.6;">
            {{ $slot }}
        </div>
        @endif

    </td>
</tr>
