@php
    $settings = \App\Models\Setting::first();
@endphp
<!doctype html>
<html lang="en" dir="ltr">

<head>
    <!-- META DATA -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="{!! strip_tags($settings->description ?? '') !!}">
    <meta name="author" content="{{ $settings->author ?? '' }}">
    <meta name="keywords" content="{!! strip_tags($settings->keywords ?? '') !!}">

    <!-- TITLE -->
    <title>{{ config('app.name') }} - {{ $title ?? $settings->title ?? '' }}</title>

    <!-- FAVICON -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset($settings->favicon ?? 'default/logo.svg') }}" />

    <!-- BOOTSTRAP CSS -->
    <link id="style" href="{{ asset('backend') }}/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />

    <!-- STYLE CSS -->
    <link href="{{ asset('backend') }}/css/style.css" rel="stylesheet" />

    <!--- FONT-ICONS CSS -->
    <link href="{{ asset('backend') }}/plugins/icons/icons.css" rel="stylesheet" />

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #334155;
        }

        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* ── Auth Card ── */
        .auth-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
            max-width: 420px;
            width: 100%;
            padding: 40px;
            border: 1px solid #e2e8f0;
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 28px;
        }

        .auth-logo img {
            height: 56px;
            width: auto;
        }

        .auth-title {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 6px;
            text-align: center;
        }

        .auth-subtitle {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 28px;
            text-align: center;
        }

        .auth-links {
            margin-top: 24px;
            text-align: center;
            font-size: 14px;
        }

        .auth-links a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }

        .auth-links a:hover {
            text-decoration: underline;
        }

        /* ── Bootstrap overrides for auth pages ── */
        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            height: 44px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        .btn-primary {
            height: 44px;
            background-color: #8fbd56;
            border-color: #8fbd56;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background-color: #1e293b;
            border-color: #1e293b;
            transform: translateY(-1px);
        }

        .header-brand-img {
            height: 3rem;
            margin-bottom: 1.5rem;
        }

        /* OTP input centering */
        .ls-2 {
            letter-spacing: 3px;
        }
    </style>

</head>

<body class="ltr">

    <!-- PAGE -->
    <div class="page">
        @yield('content')
    </div>



    <script src="{{ asset('backend') }}/plugins/jquery/jquery.min.js"></script>
    <script src="{{ asset('backend') }}/plugins/bootstrap/js/popper.min.js"></script>
    <script src="{{ asset('backend') }}/plugins/bootstrap/js/bootstrap.min.js"></script>

    <!-- CUSTOM JS -->
    
    <script src="{{ asset('backend') }}/js/custom.js"></script>

</body>

</html>