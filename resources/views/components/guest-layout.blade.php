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
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
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

        <!-- Bootstrap CSS CDN -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- FontAwesome Icons CDN -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <style>
            body {
                font-family: 'Inter', sans-serif;
                background-color: #f8f9fa;
            }
            .glow-1 {
                position: absolute;
                top: -10rem;
                right: -10rem;
                width: 30rem;
                height: 30rem;
                border-radius: 50%;
                background: rgba(13, 110, 253, 0.05);
                filter: blur(100px);
                pointer-events: none;
            }
            .glow-2 {
                position: absolute;
                bottom: -10rem;
                left: -10rem;
                width: 30rem;
                height: 30rem;
                border-radius: 50%;
                background: rgba(111, 66, 193, 0.05);
                filter: blur(100px);
                pointer-events: none;
            }
        </style>
        
        <!-- Scripts -->
        @vite(['resources/js/app.js'])
    </head>
    <body class="d-flex flex-column justify-content-center min-vh-100 py-5 position-relative overflow-hidden">
        <!-- Modern background glow effects -->
        <div class="glow-1"></div>
        <div class="glow-2"></div>

        <div class="container position-relative z-1">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4 text-center mb-4">
                    <a href="/">
                        <img src="{{ asset($logoPath) }}" class="img-fluid" style="max-height: 56px; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" alt="Logo">
                    </a>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
                    <div class="card shadow border-0 py-4 px-3 px-sm-4 bg-white bg-opacity-75 backdrop-blur-md">
                        <div class="card-body">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS CDN -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
