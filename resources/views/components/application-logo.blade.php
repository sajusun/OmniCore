@php
    $logoPath = $logo ?? ($settings->logo ?? 'default/logo.png');
@endphp
<img src="{{ asset($logoPath) }}" {{ $attributes }} alt="Logo">
