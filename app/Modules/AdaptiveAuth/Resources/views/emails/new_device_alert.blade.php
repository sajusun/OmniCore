<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Device Sign-In Notification</title>
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
            background: #111827;
            padding: 24px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
        }
        .badge {
            display: inline-block;
            background: #10b981;
            color: #ffffff;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 9999px;
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .content {
            padding: 28px 24px;
        }
        .device-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin: 20px 0;
            font-size: 14px;
            line-height: 1.6;
        }
        .device-card div {
            margin-bottom: 6px;
        }
        .device-card strong {
            color: #334155;
            display: inline-block;
            width: 110px;
        }
        .footer {
            background: #f9fafb;
            padding: 16px 24px;
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
            <h1>Security Alert: New Device Verified</h1>
            <span class="badge">Device Trusted</span>
        </div>
        <div class="content">
            <p style="font-size: 15px; margin-top: 0;">Hello <strong>{{ $userName }}</strong>,</p>
            <p style="font-size: 14px; line-height: 1.5; color: #4b5563;">
                Your account was just accessed from a new device that has been successfully verified and added to your trusted devices list:
            </p>

            <div class="device-card">
                <div><strong>Device:</strong> {{ $device->device_name }}</div>
                <div><strong>Platform:</strong> {{ $device->platform ?? 'Unknown' }} ({{ $device->device_type }})</div>
                <div><strong>IP Address:</strong> {{ $device->last_ip }}</div>
                <div><strong>Location:</strong> {{ ($device->city ?? 'Unknown') . ', ' . ($device->country ?? 'Unknown') }}</div>
                <div><strong>Signed In At:</strong> {{ $device->last_active_at?->toDayDateTimeString() ?? now()->toDayDateTimeString() }}</div>
            </div>

            <p style="font-size: 13px; color: #dc2626; font-weight: 500;">
                Don't recognize this sign-in?
            </p>
            <p style="font-size: 13px; color: #4b5563; line-height: 1.5; margin-bottom: 0;">
                If you did not author this action, your credentials may be compromised. Please sign in to your dashboard to revoke this device under <em>Security Settings</em> and update your account password right away.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved. Adaptive Security System.
        </div>
    </div>
</body>
</html>
