@props(['active'])

@php
$classes = ($active ?? false)
            ? 'nav-link active font-weight-bold text-primary border-bottom border-primary pb-2'
            : 'nav-link text-muted pb-2';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
