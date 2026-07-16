@props([
    'active' => false,
    'title'
])

@php
    $iconClasses = $active
        ? 'text-primary'
        : 'text-muted';
@endphp

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }">
    <button @click="open = !open"
            class="w-100 d-flex align-items-center justify-content-between px-3 py-2.5 border-0 bg-transparent text-decoration-none transition-all {{ $active ? 'text-primary font-weight-bold' : 'text-dark' }}"
            :class="!sidebarDesktopOpen ? 'justify-content-center' : ''">
        <div class="d-flex align-items-center">
            @if(isset($icon))
                <div class="flex-shrink-0 transition-colors {{ $iconClasses }}"
                     :class="sidebarDesktopOpen ? 'me-3' : ''" style="width: 20px; text-align: center;">
                    {{ $icon }}
                </div>
            @endif
            <span class="transition-opacity small" :class="!sidebarDesktopOpen ? 'd-none' : ''">
                {{ $title }}
            </span>
        </div>
        <i class="fas fa-chevron-right small text-muted transition-transform"
           :class="open ? 'rotate-90' : ''"
           x-show="sidebarDesktopOpen"
           style="transition: transform 0.2s;"></i>
    </button>
    <div x-show="open" class="d-flex flex-column gap-1 mt-1 ps-4 pe-2" :class="!sidebarDesktopOpen ? 'd-none' : ''">
        {{ $slot }}
    </div>
</div>
