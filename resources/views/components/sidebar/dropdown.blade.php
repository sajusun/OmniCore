@props([
    'active' => false,
    'title'
])

<li class="slide {{ $active ? 'is-expanded' : '' }}">
    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
        @if(isset($icon))
            <span class="side-menu__icon">
                {{ $icon }}
            </span>
        @endif
        <span class="side-menu__label">{{ $title }}</span>
        <i class="angle fe fe-chevron-right"></i>
    </a>
    <ul class="slide-menu">
        {{ $slot }}
    </ul>
</li>