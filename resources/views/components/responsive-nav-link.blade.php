@props(['active'])

@php
$classes = ($active ?? false)
            ? 'list-group-item list-group-item-action active px-3 py-2 border-0'
            : 'list-group-item list-group-item-action px-3 py-2 border-0 text-muted';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
