@props([
    'icon'     => '&#9993;',
    'title'    => '',
    'subtitle' => null,
])
{{--
    ROW-LEVEL COMPONENT: renders one or more <tr> elements.
    Must be placed as a direct child of the email card <table>
    (inside <x-bootstrap-mail.layout>).
--}}
<tr>
    <td class="email-header" align="center"
        style="background: #0d6efd;
               background: linear-gradient(135deg, #0a58ca 0%, #0d6efd 50%, #3d8bfd 100%);
               padding: 40px 40px 32px;
               text-align: center;">

        {{-- OUTLOOK (MSO): icon badge --}}
        <!--[if mso]>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
        <tr>
            <td align="center" valign="middle" width="60" height="60"
                style="background-color: #084298;
                       font-family: Arial, Helvetica, sans-serif;
                       font-size: 26px;
                       line-height: 60px;
                       text-align: center;">
                {!! $icon !!}
            </td>
        </tr>
        </table>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="1">
        <tr><td height="16" style="font-size: 0; line-height: 0; mso-line-height-rule: exactly;">&nbsp;</td></tr>
        </table>
        <![endif]-->
        <!--[if !mso]><!-->
        <div style="display: inline-block; width: 60px; height: 60px;
                    background-color: rgba(255,255,255,0.15); border-radius: 14px;
                    font-size: 26px; line-height: 60px; text-align: center;
                    vertical-align: top; margin-bottom: 16px;">
            {!! $icon !!}
        </div>
        <!--<![endif]-->

        {{-- TITLE --}}
        <h1 style="margin: 0;
                   color: #ffffff;
                   font-family: Arial, Helvetica, sans-serif;
                   font-size: 22px;
                   font-weight: 700;
                   line-height: 1.3;
                   letter-spacing: -0.3px;
                   mso-line-height-rule: exactly;">
            {{ $title }}
        </h1>

        @if ($subtitle)
        <p style="margin: 10px 0 0;
                  color: rgba(255,255,255,0.75);
                  font-family: Arial, Helvetica, sans-serif;
                  font-size: 14px;
                  line-height: 1.5;
                  mso-line-height-rule: exactly;">
            {{ $subtitle }}
        </p>
        @endif

    </td>
</tr>
