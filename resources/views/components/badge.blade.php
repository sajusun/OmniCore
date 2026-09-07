@props([
    'color' => 'primary',
    'soft' => true,
    'pill' => true,
])

@php
    $colorMap = [
        'success' => $soft ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-20' : 'bg-success text-white',
        'danger' => $soft ? 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20' : 'bg-danger text-white',
        'warning' => $soft ? 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20' : 'bg-warning text-dark',
        'info' => $soft ? 'bg-info bg-opacity-10 text-info border border-info border-opacity-20' : 'bg-info text-white',
        'primary' => $soft ? 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20' : 'bg-primary text-white',
        'secondary' => $soft ? 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-20' : 'bg-secondary text-white',
        'dark' => $soft ? 'bg-dark bg-opacity-10 text-dark border border-dark border-opacity-20' : 'bg-dark text-white',
    ];

    $classes = 'badge px-2.5 py-1.5 ' . ($pill ? 'rounded-pill ' : 'rounded ') . ($colorMap[$color] ?? $colorMap['primary']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
