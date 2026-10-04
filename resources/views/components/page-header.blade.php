@props([
    'title',
    'subtitle' => null,
    'breadcrumbs' => []
])

<div {{ $attributes->merge(['class' => 'd-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4']) }}>
    <div>
        @if(!empty($breadcrumbs))
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.8125rem;">
                    @foreach($breadcrumbs as $label => $url)
                        @if($loop->last || is_null($url))
                            <li class="breadcrumb-item active fw-medium" aria-current="page">{{ $label }}</li>
                        @else
                            <li class="breadcrumb-item">
                                <a href="{{ $url }}" class="text-decoration-none text-muted transition-colors">{{ $label }}</a>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        @endif
        
        <h1 class="h4 fw-bold mb-0 text-dark" style="color: var(--theme-heading-color, inherit) !important;">
            {{ $title }}
        </h1>
        
        @if($subtitle)
            <p class="text-muted small mb-0 mt-1">{{ $subtitle }}</p>
        @endif
    </div>

    @if(isset($actions))
        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{ $actions }}
        </div>
    @endif
</div>
