@props([
    'title'     => config('app.name', 'Notification'),
    'preheader' => null,
])
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      xmlns="http://www.w3.org/1999/xhtml"
      xmlns:v="urn:schemas-microsoft-com:vml"
      xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="format-detection" content="telephone=no,date=no,address=no,email=no,url=no" />
    <meta name="color-scheme" content="light" />
    <meta name="supported-color-schemes" content="light" />
    <title>{{ $title }}</title>

    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
                <o:AllowPNG/>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->

    <style type="text/css">
        body, #bodyTable {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        body {
            background-color: #f8f9fa;
        }
        table, td {
            border-collapse: collapse !important;
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }
        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }
        u + #body a {
            color: inherit;
            text-decoration: none;
            font-size: inherit;
            font-family: inherit;
            font-weight: inherit;
            line-height: inherit;
        }
        #MessageViewBody a {
            color: inherit;
            text-decoration: none;
            font-size: inherit;
            font-family: inherit;
            font-weight: inherit;
            line-height: inherit;
        }

        @media only screen and (max-width: 620px) {
            .email-wrapper {
                padding: 16px 8px !important;
            }
            .email-card {
                width: 100% !important;
                border-radius: 0 !important;
            }
            .email-header {
                padding: 28px 20px 24px !important;
            }
            .email-body {
                padding: 28px 20px 20px !important;
            }
            .email-footer-cell {
                padding: 20px 20px 28px !important;
            }
            .btn-cta {
                padding: 13px 24px !important;
                font-size: 14px !important;
            }
            .otp-code {
                font-size: 32px !important;
                letter-spacing: 6px !important;
            }
        }
    </style>
</head>
<body id="body"
      style="margin: 0; padding: 0; background-color: #f8f9fa; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">

    @if ($preheader)
    <div aria-hidden="true"
         style="display: none; font-size: 1px; color: #f8f9fa; line-height: 1px;
                max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all;">
        {{ $preheader }}&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
    </div>
    @endif

    <!--[if mso | IE]>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
           style="background-color: #f8f9fa;">
    <tr><td>
    <![endif]-->

    <table id="bodyTable" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
           style="background-color: #f8f9fa; min-width: 100%; margin: 0; padding: 0;">
        <tr>
            <td class="email-wrapper" align="center" valign="top"
                style="padding: 40px 16px;">

                <!--[if mso]>
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center">
                <tr><td>
                <![endif]-->

                <table class="email-card" role="presentation" cellspacing="0" cellpadding="0" border="0"
                       width="600" align="center"
                       style="max-width: 600px; width: 100%; background-color: #ffffff;
                              border-radius: 12px; overflow: hidden; border: 1px solid #dee2e6; box-shadow: 0 4px 6px rgba(0,0,0,0.015);">
                    {{ $slot }}
                </table>

                <!--[if mso]>
                </td></tr>
                </table>
                <![endif]-->

            </td>
        </tr>
    </table>

    <!--[if mso | IE]>
    </td></tr>
    </table>
    <![endif]-->

</body>
</html>
