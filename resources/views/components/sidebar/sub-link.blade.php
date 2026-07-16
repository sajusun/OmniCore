@props([
    'href',
    'active' => false
])

<li>
    <a href="{{ $href }}"
       {{ $attributes->merge(['class' => 'slide-item' . ($active ? ' active' : '')]) }}>
        {{ $slot }}
    </a>
</li>
