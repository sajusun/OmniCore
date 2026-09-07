@props([
    'url'      => '#',
    'label'    => 'Click Here',
    'color'    => '#0d6efd',         {{-- Bootstrap primary --}}
    'colorEnd' => '#0a58ca',         {{-- Bootstrap primary dark --}}
    'align'    => 'center',          {{-- left | center | right --}}
])
{{--
    CONTENT-LEVEL COMPONENT: renders a full-width table with a centered button.
    Use inside <x-bootstrap-mail.body>.

    OUTLOOK: Uses VML v:roundrect for  gradient button.
    MODERN:  Uses a standard <a> tag with inline gradient.
--}}
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
       style="margin-bottom: 24px;">
    <tr>
        <td align="{{ $align }}">

            {{-- OUTLOOK (MSO): VML  rectangle button. --}}
            <!--[if mso]>
            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml"
                         xmlns:w="urn:schemas-microsoft-com:office:word"
                         href="{{ $url }}"
                         style="height: 52px; v-text-anchor: middle; width: 224px;"
                         arcsize="19%"
                         fill="t"
                         stroke="f">
                <v:fill type="gradient"
                        color="{{ $color }}"
                        color2="{{ $colorEnd }}"
                        angle="135" />
                <w:anchorlock/>
                <center style="color: #ffffff;
                               font-family: Arial, Helvetica, sans-serif;
                               font-size: 15px;
                               font-weight: 700;
                               letter-spacing: 0.3px;">
                    {{ $label }}
                </center>
            </v:roundrect>
            <![endif]-->

            <!--[if !mso]><!-->
            <a href="{{ $url }}"
               class="btn-cta"
               style="display: inline-block;
                      background-color: {{ $color }};
                      background: linear-gradient(135deg, {{ $color }} 0%, {{ $colorEnd }} 100%);
                      color: #ffffff;
                      font-family: Arial, Helvetica, sans-serif;
                      font-size: 15px;
                      font-weight: 700;
                      text-decoration: none;
                      padding: 15px 36px;
                      border-radius: 10px;
                      letter-spacing: 0.3px;
                      line-height: 1;
                      mso-padding-alt: 0;">
                {{ $label }}
            </a>
            <!--<![endif]-->

        </td>
    </tr>
</table>
