<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification - {{ config('app.name') }}</title>
</head>

<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f4f6f9;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="background:#f4f6f9;padding:40px 20px;">
        <tr>
            <td align="center">

                <!-- MAIN CONTAINER -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                    style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.08);overflow:hidden;">

                    <!-- HEADER – Gradient (Uncomment if you want a purple header) -->
                    <tr>
                        <td
                            style="background:linear-gradient(135deg,#521aac 0%,#521aac 100%);padding:50px 30px;text-align:center;">
                            <h1 style="margin:0;color:#ffffff;font-size:32px;font-weight:700;letter-spacing:-0.8px;">
                                {{ $title ?? "Verify Your Account" }}
                            </h1>
                        </td>
                    </tr>

                    <!-- MAIN CONTENT -->
                    <tr>
                        <td style="padding:45px 35px;text-align:center;">

                            <h2 style="margin:0 0 25px;color:#333;font-size:26px;font-weight:600;">
                                Your One-Time Verification Code
                            </h2>

                            <p style="margin:0 0 20px;color:#555;font-size:16px;line-height:1.8;">
                                Hello there,<br><br>
                                You requested a verification code for your account at <strong>{{ config('app.name') }},
                                    Inc.</strong>
                            </p>

                            <p style="margin:30px 0 25px;color:#555;font-size:16px;line-height:1.8;">
                                Please use the code below to complete your verification. It will expire in <strong>10
                                    minutes</strong> for security reasons.
                            </p>

                            <!-- OTP CODE BOX -->
                            <div
                                style="margin:40px auto;padding:20px 0;max-width:280px;background:#f8f5ff;border:2px dashed #521aac;border-radius:12px;">
                                <p style="margin:0;font-size:42px;font-weight:700;letter-spacing:8px;color:#521aac;">
                                    {{ $otp }}
                                </p>
                            </div>

                            <p style="margin:35px 0 0;color:#777;font-size:15px;line-height:1.7;">
                                Thank you for keeping your account secure!<br>
                                <strong>{{ config('app.name') }} Team</strong> 💜
                            </p>

                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td
                            style="background:#f8fafc;padding:35px 30px;border-top:1px solid #e5e7eb;text-align:center;">
                            <p style="margin:0 0 12px;color:#999;font-size:14px;line-height:1.6;">
                                This is an automated email containing your one-time verification code.
                            </p>
                            <p style="margin:15px 0 0;color:#aaa;font-size:13px;">
                                © {{ date('Y') }} {{ config('app.name') }}, Inc. All rights reserved.
                            </p>
                            <p style="margin:20px 0 0;color:#bbb;font-size:12px;">
                                Please do not reply to this email. For support, contact us via the website.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
