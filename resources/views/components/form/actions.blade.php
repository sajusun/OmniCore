@props([
    'submitText' => 'Save Changes',
    'submitIcon' => null,
    'info' => 'All changes are saved immediately',
    'cancelUrl' => null,
    'cancelText' => 'Cancel',
    'sticky' => false,
])

<div {{ $attributes->merge(['class' => 'mt-4 pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-3 ' . ($sticky ? 'sticky-bottom bg-white py-3 px-4 shadow-sm border' : '')]) }}
     style="border-color: var(--theme-card-border, #e2e8f0) !important;">
    
    @if($info)
        <p class="small text-muted d-flex align-items-center gap-1.5 mb-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-primary flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $info }}</span>
        </p>
    @else
        <div></div>
    @endif

    <div class="d-flex align-items-center gap-2 ms-auto">
        {{ $slot }}

        @if($cancelUrl)
            <a href="{{ $cancelUrl }}" class="btn btn-outline-secondary px-4 py-2 small fw-medium">
                {{ $cancelText }}
            </a>
        @endif

        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2 small fw-semibold shadow-sm">
            @if($submitIcon)
                {!! $submitIcon !!}
            @else
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            @endif
            <span>{{ $submitText }}</span>
        </button>
    </div>
</div>
