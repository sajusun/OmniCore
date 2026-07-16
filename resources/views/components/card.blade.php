@props(['title' => null, 'noPadding' => false])

<div {{ $attributes->merge(['class' => 'card shadow-sm border-light overflow-hidden']) }}>
    @if($title || isset($header))
        <div class="card-header bg-light d-flex justify-content-between align-items-center px-4 py-3 border-bottom border-light">
            @if($title)
                <h5 class="card-title mb-0 font-weight-bold">{{ $title }}</h5>
            @endif
            {{ $header ?? '' }}
        </div>
    @endif
    
    <div class="{{ $noPadding ? '' : 'card-body p-4' }}">
        {{ $slot }}
    </div>
</div>
