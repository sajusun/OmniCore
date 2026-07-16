@props([
    'href',
    'active' => false
])

@php
    $baseClasses = 'd-block py-2 px-3 text-decoration-none rounded-2 transition-all small';
    $activeClasses = $active
        ? 'text-primary font-weight-bold bg-primary bg-opacity-10'
        : 'text-secondary hover-bg-light';
@endphp

<a href="{{ $href }}"
   {{ $attributes->merge(['class' => "$baseClasses $activeClasses"]) }}>
    {{ $slot }}
</a>
