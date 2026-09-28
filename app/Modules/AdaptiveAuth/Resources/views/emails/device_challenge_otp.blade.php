<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Verification Code</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 24px;
            color: #1f2937;
        }
        .container {
            max-width: 540px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            padding: 28px 24px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        .content {
            padding: 32px 28px;
        }
        .otp-box {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 10px;
            text-align: center;
            padding: 20px;
            margin: 24px 0;
        }
        .otp-code {
            font-size: 34px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #4f46e5;
            margin: 0;
            font-family: 'Courier New', Courier, monospace;
        }
        .device-info {
            background-color: #f9fafb;
            border-left: 4px solid #3b82f6;
            border-radius: 4px;
            padding: 14px 16px;
            margin: 20px 0;
            font-size: 13px;
            line-height: 1.6;
            color: #4b5563;
        }
        .device-info strong {
            color: #111827;
        }
        .footer {
            background: #f9fafb;
            padding: 18px 24px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Adaptive Security Verification</h1>
        </div>
        <div class="content">
            <p style="font-size: 15px; margin-top: 0;">Hello,</p>
            <p style="font-size: 14px; line-height: 1.5; color: #4b5563;">
                We detected a sign-in attempt from a <strong>new device or unrecognized location</strong>. To ensure the security of your account, please enter the following verification code:
            </p>

            <div class="otp-box">
                <p class="otp-code">{{ $otpCode }}</p>
                <span style="font-size: 12px; color: #64748b;">Valid for {{ $expiresInMinutes }} minutes</span>
            </div>

            <div class="device-info">
                <div><strong>Device:</strong> {{ $deviceInfo['device_name'] ?? 'Unknown' }}</div>
                <div><strong>IP Address:</strong> {{ $deviceInfo['ip'] ?? 'Unknown' }}</div>
                <div><strong>Location:</strong> {{ ($deviceInfo['city'] ?? 'Unknown') . ', ' . ($deviceInfo['country'] ?? 'Unknown') }}</div>
                <div><strong>Time:</strong> {{ now()->toDayDateTimeString() }}</div>
            </div>

            <p style="font-size: 13px; color: #6b7280; margin-bottom: 0;">
                If you did not initiate this sign-in attempt, someone may have obtained your password. Please reset your password immediately and review your account security.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved. Adaptive Security System.
        </div>
    </div>
</body>
</html>
