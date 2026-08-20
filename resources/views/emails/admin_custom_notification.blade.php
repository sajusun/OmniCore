<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectText }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #333333;
        }

        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .email-header {
            background-color: #8fbd56;
            padding: 24px;
            text-align: center;
            color: #ffffff;
        }

        .email-header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }

        .email-body {
            padding: 30px 24px;
            line-height: 1.6;
            font-size: 15px;
        }

        .custom-mail-content {
            margin-top: 15px;
            margin-bottom: 20px;
            color: #2d3748;
        }

        .custom-mail-content p {
            margin-bottom: 12px;
            line-height: 1.6;
        }

        .custom-mail-content ul,
        .custom-mail-content ol {
            padding-left: 20px;
            margin-bottom: 15px;
        }

        .custom-mail-content li {
            margin-bottom: 6px;
        }

        .email-footer {
            background-color: #f9fafb;
            padding: 16px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #edf2f7;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>{{ config('app.name', 'App') }} Notification</h1>
        </div>
        <div class="email-body">
            @if ($recipientName)
            <p>Hello <strong>{{ $recipientName }}</strong>,</p>
            @endif
            <div class="custom-mail-content">
                {!! $messageBody !!}
            </div>
            @if ($recipientName)
            <p style="margin-top: 30px;">Best regards,<br>The {{ config('app.name', 'App') }} Team</p>
            @endif
        </div>
        <div class="email-footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'App') }}. All rights reserved.
        </div>
    </div>
</body>

</html>