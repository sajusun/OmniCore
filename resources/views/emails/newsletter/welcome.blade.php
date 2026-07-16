<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
</head>

<body style="font-family: Arial, sans-serif; background:#f4f6f8; padding:20px;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" style="background:#ffffff; padding:30px; border-radius:8px;">
                    <tr>
                        <td>
                            <h2 style="color:#333;">Welcome, {{ $user->name }}</h2>

                            <p style="color:#555; line-height:1.6;">
                                Your email has been successfully verified and your account is now active.
                            </p>

                            <p style="color:#555;">
                                You can now log in and start using all our features.
                            </p>

                            <div style="text-align:center; margin:30px 0;">
                                <a href="{{ $url }}" style="background:#0d6efd; color:#ffffff; padding:12px 24px;
                                          text-decoration:none; border-radius:5px; font-weight:bold;">
                                    Login to Your Account
                                </a>
                            </div>

                            <p style="color:#777; font-size:14px;">
                                If the button doesn’t work, copy and paste this URL into your browser:
                            </p>

                            <p style="word-break:break-all;">
                                <a href="{{ $url }}">{{ $url }}</a>
                            </p>

                            <hr style="margin:30px 0;">

                            <p style="color:#999; font-size:12px;">
                                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>