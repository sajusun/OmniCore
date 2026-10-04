@props([
    'title',
    'subtitle' => null,
    'breadcrumbs' => []
])

<div {{ $attributes->merge(['class' => 'd-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4']) }}>
    <div>
        @if(!empty($breadcrumbs))
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0 align-items-center" style="font-size: 0.8125rem;">
                    @foreach($breadcrumbs as $label => $url)
                        @if($loop->last || is_null($url))
                            <li class="breadcrumb-item active text-dark fw-medium" aria-current="page" style="color: var(--theme-body-color, #475569) !important;">
                                {{ $label }}
                            </li>
                        @else
                            <li class="breadcrumb-item">
                                <a href="{{ $url }}" class="text-decoration-none text-muted transition-colors hover-primary" style="color: var(--theme-muted-color, #64748b) !important;">
                                    {{ $label }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        @endif

        <h4 class="fw-bold mb-0" style="font-size: 1.25rem; color: var(--theme-heading-color, #0f172a) !important; letter-spacing: -0.01em;">
            {{ $title }}
        </h4>

        @if($subtitle)
            <p class="text-muted small mb-0 mt-1" style="font-size: 0.8125rem;">{{ $subtitle }}</p>
        @endif
    </div>

    @if(isset($actions))
        <div class="d-flex align-items-center gap-2 flex-wrap flex-shrink-0">
            {{ $actions }}
        </div>
    @endif
</div>
