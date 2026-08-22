<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectText }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            color: #1f2937;
            -webkit-text-size-adjust: none;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f3f4f6;
            padding: 30px 15px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            border: 1px solid #e5e7eb;
        }

        .email-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 32px 24px;
            text-align: center;
            color: #ffffff;
        }

        .email-logo {
            width: 85px;
            height: auto;
            margin-bottom: 12px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .email-header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
        }

        .email-header p {
            margin: 6px 0 0 0;
            font-size: 13px;
            color: #94a3b8;
        }

        .email-body {
            padding: 32px 28px;
            line-height: 1.7;
            font-size: 15px;
            color: #334155;
        }

        .greeting {
            font-size: 17px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 16px;
        }

        .custom-mail-content {
            margin-top: 15px;
            margin-bottom: 25px;
            color: #334155;
        }

        .custom-mail-content p {
            margin-bottom: 14px;
            line-height: 1.7;
        }

        .custom-mail-content ul,
        .custom-mail-content ol {
            padding-left: 24px;
            margin-bottom: 16px;
        }

        .custom-mail-content li {
            margin-bottom: 8px;
        }

        .custom-mail-content a {
            color: #2563eb;
            text-decoration: underline;
        }

        .email-footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #f1f5f9;
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="email-header">
                @php
                    $logoPath = public_path('default/jamieb-sq-logo.png');
                    $hasLogo = file_exists($logoPath);
                @endphp
                @if($hasLogo)
                    <img src="{{ $message->embed($logoPath) }}" alt="{{ config('app.name', 'CruzeHub') }}" class="email-logo">
                @else
                    <img src="{{ asset('default/jamieb-sq-logo.png') }}" alt="{{ config('app.name', 'CruzeHub') }}" class="email-logo">
                @endif
                <h1>{{ config('app.name', 'CruzeHub') }}</h1>
                <p>{{ $subjectText }}</p>
            </div>
            <div class="email-body">
                @if ($recipientName)
                    <div class="greeting">Hello {{ $recipientName }},</div>
                @endif

                <div class="custom-mail-content">
                    {!! $messageBody !!}
                </div>

                <div style="margin-top: 32px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 14px; color: #64748b;">
                    Best regards,<br>
                    <strong style="color: #0f172a;">The {{ config('app.name', 'CruzeHub') }} Team</strong>
                </div>
            </div>
            <div class="email-footer">
                &copy; {{ date('Y') }} {{ config('app.name', 'CruzeHub') }}. All rights reserved.<br>
                This is an automated notification sent from the {{ config('app.name', 'CruzeHub') }} Admin Panel.
            </div>
        </div>
    </div>
</body>

</html>