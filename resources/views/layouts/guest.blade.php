@props(['title' => null])
@php
    $settings = settings();
    $logoPath = $settings->logo ?? 'default/logo.png';
    $faviconPath = $settings->favicon ?? 'default/logo.svg';
    $description = strip_tags($settings->description ?? '');
    $keywords = strip_tags($settings->keywords ?? '');
    $author = $settings->author ?? '';
    $pageTitle = $title ?? ($settings->title ?? 'Welcome');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $description }}">
    <meta name="keywords" content="{{ $keywords }}">
    <meta name="author" content="{{ $author }}">

    <title>{{ config('app.name', 'Laravel') }} - {{ $pageTitle }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset($faviconPath) }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light min-vh-100 d-flex flex-column justify-content-center py-5 position-relative overflow-hidden">

    <!-- Background glow effects -->
    <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden" style="z-index: 0; pointer-events: none;">
        <div class="position-absolute rounded-circle"
            style="width: 500px; height: 500px; top: -160px; right: -160px; background: rgba(99,102,241,0.07); filter: blur(80px);"></div>
        <div class="position-absolute rounded-circle"
            style="width: 500px; height: 500px; bottom: -160px; left: -160px; background: rgba(139,92,246,0.07); filter: blur(80px);"></div>
    </div>

    <!-- Logo -->
    <div class="text-center mb-4" style="position: relative; z-index: 1;">
        <a href="/">
            <img src="{{ asset($logoPath) }}" alt="Logo"
                class="img-fluid"
                style="height: 56px; width: auto; object-fit: contain; transition: transform 0.3s ease;"
                onmouseover="this.style.transform='scale(1.05)'"
                onmouseout="this.style.transform='scale(1)'">
        </a>
    </div>

    <!-- Card Slot -->
    <div class="mx-auto px-3 w-100" style="max-width: 420px; position: relative; z-index: 1;">
        <div class="card border shadow-sm rounded-4 p-4">
            {{ $slot }}
        </div>
    </div>

</body>

</html>
