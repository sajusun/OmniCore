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
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
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
    <body class="font-sans antialiased text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-900 min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Modern background glow effects -->
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-[500px] h-[500px] rounded-full bg-indigo-500/10 blur-[100px] dark:bg-indigo-500/5"></div>
            <div class="absolute -bottom-40 -left-40 w-[500px] h-[500px] rounded-full bg-violet-500/10 blur-[100px] dark:bg-violet-500/5"></div>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md z-10">
            <div class="flex justify-center mb-6">
                <a href="/">
                    <img src="{{ asset($logoPath) }}" class="h-14 w-auto object-contain hover:scale-105 transition-transform duration-300" alt="Logo">
                </a>
            </div>
        </div>

        <div class="mt-2 sm:mx-auto sm:w-full sm:max-w-sm z-10 px-4">
            <div class="bg-white dark:bg-slate-800/80 py-8 px-6 shadow-2xl shadow-slate-200/50 dark:shadow-none sm:rounded-2xl sm:px-10 border border-slate-100 dark:border-slate-700/50 backdrop-blur-md">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
