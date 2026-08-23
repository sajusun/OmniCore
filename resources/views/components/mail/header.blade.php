@props([
    'icon'     => '&#9993;',
    'title'    => '',
    'subtitle' => null,
])
{{--
    ROW-LEVEL COMPONENT: renders one or more <tr> elements.
    Must be placed as a direct child of the email card <table>
    (inside <x-mail.layout>).
--}}
<tr>
    <td class="email-header" align="center"
        style="background: #8fbd56;
               background: linear-gradient(135deg, #7cb342 0%, #8fbd56 50%, #9bcc65 100%);
               padding: 22px 24px 20px;
               text-align: center;">

        @if ($icon)
        <!--[if mso]>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
        <tr>
            <td align="center" valign="middle" width="44" height="44"
                style="background-color: rgba(255,255,255,0.2);
                       font-family: Arial, Helvetica, sans-serif;
                       font-size: 20px;
                       line-height: 44px;
                       text-align: center;">
                {!! $icon !!}
            </td>
        </tr>
        </table>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="1">
        <tr><td height="10" style="font-size: 0; line-height: 0; mso-line-height-rule: exactly;">&nbsp;</td></tr>
        </table>
        <![endif]-->
        <!--[if !mso]><!-->
        <div style="display: inline-block; width: 44px; height: 44px;
                    background-color: rgba(255,255,255,0.2); border-radius: 10px;
                    font-size: 20px; line-height: 44px; text-align: center;
                    vertical-align: top; margin-bottom: 10px;">
            {!! $icon !!}
        </div>
        <!--<![endif]-->
        @endif

        {{-- TITLE --}}
        <h1 style="margin: 0;
                   color: #ffffff;
                   font-family: Arial, Helvetica, sans-serif;
                   font-size: 20px;
                   font-weight: 700;
                   line-height: 1.3;
                   letter-spacing: -0.3px;
                   mso-line-height-rule: exactly;">
            {{ $title }}
        </h1>

        @if ($subtitle)
        <p style="margin: 6px 0 0;
                  color: rgba(255,255,255,0.9);
                  font-family: Arial, Helvetica, sans-serif;
                  font-size: 13px;
                  line-height: 1.4;
                  mso-line-height-rule: exactly;">
            {{ $subtitle }}
        </p>
        @endif

    </td>
</tr>
