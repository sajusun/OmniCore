@props([
    'href' => null
])

@if($href)
    <a
        href="{{ $href }}"
        {{ $attributes->merge(['class' => 'btn btn-secondary']) }}
    >
        {{ $slot ?? 'Cancel' }}
    </a>
@else
    <button
        type="button"
        {{ $attributes->merge(['class' => 'btn btn-secondary']) }}
    >
        {{ $slot ?? 'Cancel' }}
    </button>
@endif
