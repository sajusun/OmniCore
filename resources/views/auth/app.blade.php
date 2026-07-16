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
            background-color: #f8fafc; /* Smoky White */
            color: #334155;
        }
        
        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-brand-img {
            height: 3rem;
            margin-bottom: 1.5rem;
        }
    </style>

</head>

<body class="ltr">

    <!-- PAGE -->
    <div class="page">
        @yield('content')
    </div>

    <!-- JQUERY JS -->
    <script src="{{ asset('backend') }}/plugins/jquery/jquery.min.js"></script>

    <!-- BOOTSTRAP JS -->
    <script src="{{ asset('backend') }}/plugins/bootstrap/js/popper.min.js"></script>
    <script src="{{ asset('backend') }}/plugins/bootstrap/js/bootstrap.min.js"></script>

    <!-- CUSTOM JS -->
    <script src="{{ asset('backend') }}/js/custom.js"></script>

</body>

</html>
