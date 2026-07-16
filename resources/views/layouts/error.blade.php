@props(['title' => null])

@php
    $settings = settings();
    $faviconPath = $settings->favicon ?? 'default/logo.svg';
    $description = strip_tags($settings->description ?? '');
    $keywords = strip_tags($settings->keywords ?? '');
    $author = $settings->author ?? '';
    $pageTitle = $title ?? ($settings->title ?? 'Error');
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

    <title>{{ config('app.name') }} - {{ $pageTitle }}</title>

    <link rel="shortcut icon" href="{{ asset($faviconPath) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-vh-100 bg-white text-dark">

    <main class="min-vh-100 d-flex align-items-center justify-content-center px-3">
        <div class="w-100 text-center" style="max-width: 1140px;">
            {{ $slot }}
        </div>
    </main>

</body>

</html>