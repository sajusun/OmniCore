<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reply</title>
</head>
<body>
    <p>Dear {{ $name }},</p>

    <p>{!! nl2br(e($bodyMessage)) !!}</p>

    <br>
    <p>Best regards,</p>
    <p><strong>{{ config('app.name') }} Support Team</strong></p>
</body>
</html>