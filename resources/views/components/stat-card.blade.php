@props([
    'title',
    'value',
    'color' => 'primary',
    'icon' => null
])

@php
    // Mapping Tailwind colors to Bootstrap color utilities
    $colors = [
        'indigo'  => 'bg-primary bg-opacity-10 text-primary',
        'primary' => 'bg-primary bg-opacity-10 text-primary',
        'emerald' => 'bg-success bg-opacity-10 text-success',
        'success' => 'bg-success bg-opacity-10 text-success',
        'sky'     => 'bg-info bg-opacity-10 text-info',
        'info'    => 'bg-info bg-opacity-10 text-info',
        'purple'  => 'bg-dark bg-opacity-10 text-dark',
        'blue'    => 'bg-primary bg-opacity-10 text-primary',
        'green'   => 'bg-success bg-opacity-10 text-success',
        'yellow'  => 'bg-warning bg-opacity-10 text-warning',
        'warning' => 'bg-warning bg-opacity-10 text-warning',
        'red'     => 'bg-danger bg-opacity-10 text-danger',
        'danger'  => 'bg-danger bg-opacity-10 text-danger',
    ];
    
    $colorClass = $colors[$color] ?? $colors['primary'];
@endphp

<div {{ $attributes->merge(['class' => 'card shadow-sm border-light p-4 d-flex flex-row align-items-center justify-content-between transition-transform duration-300']) }} style="transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
    <div>
        <h3 class="mb-0 font-weight-bold text-dark">{{ $value }}</h3>
        <p class="text-muted small mb-0 mt-1">{{ $title }}</p>
    </div>
    <div class="p-3 rounded-3 {{ $colorClass }} d-flex align-items-center justify-content-center">
        @if($icon)
            {!! $icon !!}
        @else
            <i class="fas fa-bolt fa-2x"></i>
        @endif
    </div>
</div>
