<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $title = $model->title ?? $model->name ?? config('app.name') . ' Shared Content';
        $description = $model->description ?? $model->content ?? $model->short_description ?? 'Check out this content on ' . config('app.name');
        $image = $model->thumbnail_url ?? $model->image_url ?? $model->image ?? asset(settings('logo') ?? 'default/logo.png');
        $url = url()->current();
    @endphp

    <title>{{ $title }} - {{ config('app.name') }}</title>
    <meta name="description" content="{{ Str::limit(strip_tags($description), 160) }}">

    <!-- OpenGraph SEO Tags -->
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($description), 200) }}">
    <meta property="og:image" content="{{ $image }}">
    <meta property="og:url" content="{{ $url }}">
    <meta property="og:type" content="article">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($description), 200) }}">
    <meta name="twitter:image" content="{{ $image }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
            font-family: system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .share-card {
            max-width: 600px;
            width: 100%;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .share-card img.preview-img {
            width: 100%;
            max-height: 320px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container p-3 d-flex justify-content-center">
        <div class="share-card">
            @if(!empty($image))
                <img src="{{ $image }}" class="preview-img" alt="{{ $title }}">
            @endif
            <div class="p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge bg-primary px-3 py-2 text-uppercase">
                        {{ class_basename($model) }}
                    </span>
                    @if(!empty($shareLink?->expires_at))
                        <small class="text-muted">Expires: {{ $shareLink->expires_at->diffForHumans() }}</small>
                    @endif
                </div>
                <h3 class="fw-bold mb-3">{{ $title }}</h3>
                <p class="text-secondary mb-4">{{ Str::limit(strip_tags($description), 300) }}</p>
                <div class="d-grid gap-2">
                    <a href="{{ url('/') }}" class="btn btn-primary py-2 fw-semibold">
                        Open in {{ config('app.name') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
