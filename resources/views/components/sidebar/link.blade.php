@props([
    'href',
    'active' => false
])

@php
    $baseClasses = 'd-flex align-items-center px-3 py-2.5 rounded-3 text-decoration-none transition-all';
    $activeClasses = $active
        ? 'bg-primary bg-opacity-10 text-primary font-weight-bold shadow-sm'
        : 'text-secondary hover-bg-light';
    
    $iconClasses = $active
        ? 'text-primary'
        : 'text-muted';
@endphp

<style>
    .hover-bg-light:hover {
        background-color: rgba(0, 0, 0, 0.04);
        color: #212529 !important;
    }
</style>

<a href="{{ $href }}"
   {{ $attributes->merge(['class' => "$baseClasses $activeClasses"]) }}
   :class="!sidebarDesktopOpen ? 'justify-content-center' : ''">
    @if(isset($icon))
        <div class="flex-shrink-0 transition-colors {{ $iconClasses }}"
             :class="sidebarDesktopOpen ? 'me-3' : ''" style="width: 20px; text-align: center;">
            {{ $icon }}
        </div>
    @endif
    <span class="transition-opacity small" :class="!sidebarDesktopOpen ? 'd-none' : ''">
        {{ $slot }}
    </span>
</a>
