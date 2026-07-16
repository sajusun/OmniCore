@php
    $logoPath = $logo ?? ($settings->logo ?? 'default/logo.png');
@endphp
<img src="{{ asset($logoPath) }}" {{ $attributes->merge(['class' => 'img-fluid']) }} alt="Logo">
