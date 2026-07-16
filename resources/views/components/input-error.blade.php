@props(['messages'])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'text-danger small mt-1']) }}>
        <ul class="list-unstyled mb-0">
            @foreach ((array) $messages as $message)
                <li><i class="fas fa-exclamation-circle me-1"></i> {{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
