<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Contact Us! - {{ config('app.name') }}</title>
</head>

<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f4f6f9;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="background:#f4f6f9;padding:40px 20px;">
        <tr>
            <td align="center">

                <!-- MAIN CONTAINER -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                    style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.08);overflow:hidden;">

                    <!-- HEADER – Gradient -->
                    <tr>
                        <td
                            style="background:linear-gradient(135deg,#521aac 0%,#521aac 100%);padding:50px 30px;text-align:center;">
                            <h1 style="margin:0;color:#ffffff;font-size:32px;font-weight:700;letter-spacing:-0.8px;">
                                Thank You, {{ $name }}!
                            </h1>
                            <p style="margin:18px 0 0;color:#e6f0ff;font-size:18px;line-height:1.5;">
                                Your message has been received successfully.
                            </p>
                        </td>
                    </tr>

                    <!-- MAIN CONTENT -->
                    <tr>
                        <td style="padding:45px 35px;text-align:center;">

                            <h2 style="margin:0 0 25px;color:#333;font-size:26px;font-weight:600;">
                                We Appreciate You Reaching Out!
                            </h2>

                            <p style="margin:0 0 20px;color:#555;font-size:16px;line-height:1.8;">
                                Hello <strong>{{ $name }}</strong>,<br><br>
                                Thank you so much for getting in touch with us at <strong>{{ config('app.name') }}</strong>
                                We’ve successfully received your message and our support team will get back to you as soon as
                                possible — usually within 24 hours.
                            </p>

                            <p style="margin:30px 0 0;color:#555;font-size:16px;line-height:1.8;">
                                While you wait, feel free to explore our platform and see how we're making a difference
                                together.
                            </p>

                            <!-- CTA BUTTON -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                                style="margin:40px auto;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ config('app.frontend_url') }}"
                                            style="display:inline-block;background:linear-gradient(135deg,#521aac 0%,#6a2ddb 100%);color:#ffffff;padding:16px 42px;text-decoration:none;border-radius:50px;font-weight:700;font-size:17px;box-shadow:0 6px 20px rgba(82,26,172,.35);transition:all .3s;">
                                            Visit {{ config('app.name') }}
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:35px 0 0;color:#777;font-size:15px;line-height:1.7;">
                                Have a wonderful day!<br>
                                <strong>{{ config('app.name') }} Team</strong>
                            </p>

                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td
                            style="background:#f8fafc;padding:35px 30px;border-top:1px solid #e5e7eb;text-align:center;">
                            <p style="margin:0 0 12px;color:#999;font-size:14px;line-height:1.6;">
                                This is an automated confirmation email from your contact form submission.
                            </p>
                            <p style="margin:15px 0 0;color:#aaa;font-size:13px;">
                                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                            <p style="margin:20px 0 0;color:#bbb;font-size:12px;">
                                If you didn’t submit this form, please ignore this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
