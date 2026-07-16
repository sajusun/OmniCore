@props([
    'href',
    'active' => false
])

<li class="slide {{ $active ? 'is-expanded' : '' }}">
    <a href="{{ $href }}"
       {{ $attributes->merge(['class' => 'side-menu__item' . ($active ? ' has-link active' : '')]) }}>
        @if(isset($icon))
            <span class="side-menu__icon">
                {{ $icon }}
            </span>
        @endif
        <span class="side-menu__label">{{ $slot }}</span>
    </a>
</li>
