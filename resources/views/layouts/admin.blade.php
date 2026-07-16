<!doctype html>
<html lang="en" dir="ltr">

<head>
    <!-- META DATA -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="{!! strip_tags(settings()->description ?? '') !!}">
    <meta name="author" content="{{ settings('author') ?? '' }}">
    <meta name="keywords" content="{!! strip_tags(settings('keywords') ?? '') !!}">

    <!-- FAVICON -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset(settings('favicon') ?? 'default/logo.png') }}" />

    <!-- TITLE -->
    <title>{{ config('app.name') }} - {{ $title ?? settings('title') ?? '' }}</title>
    <!-- Scripts -->

    @vite(['resources/js/app.js'])

    @include('backend.partials._styles')
</head>

<body class="ltr app sidebar-mini">
    @include('backend.partials._switcher')


    <div class="app-content main-content mt-0">
        <div class="side-app d-flex flex-column min-vh-100">
            @include('backend.partials._header')
            @include('backend.partials._sidebar')
            <div class="flex-grow-1">
                {{ $slot??'' }}
                @yield('content')
            </div>
            @include('backend.partials._footer')
        </div>
    </div>
    <!-- page -->
    @include('backend.partials._scripts')

</body>

</html>