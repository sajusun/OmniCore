@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'badge' => null,
    'badgeClass' => 'bg-primary',
    'noPadding' => false,
    'headerClass' => '',
    'bodyClass' => '',
])

<div {{ $attributes->merge(['class' => 'card shadow-sm border overflow-hidden']) }}
     style="background-color: var(--theme-card-bg, #ffffff); border-color: var(--theme-card-border, #e2e8f0); border-radius: var(--theme-card-radius, 12px); box-shadow: var(--theme-card-shadow, 0 4px 20px -2px rgba(0,0,0,0.05));">
    
    @if($title || isset($header) || $icon || $badge || isset($actions))
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2 px-4 py-3 border-bottom {{ $headerClass }}"
             style="background-color: var(--theme-table-header-bg, #f8fafc); border-color: var(--theme-card-border, #e2e8f0);">
            
            <div class="d-flex align-items-center gap-2">
                @if($icon)
                    <div class="p-2 rounded-2 text-primary d-inline-flex align-items-center justify-content-center"
                         style="background-color: rgba(99, 102, 241, 0.1); color: var(--primary-bg-color, #6366f1) !important;">
                        {!! $icon !!}
                    </div>
                @endif

                <div>
                    @if($title)
                        <h6 class="card-title fw-bold mb-0" style="color: var(--theme-heading-color, #0f172a); font-size: 0.95rem;">
                            {{ $title }}
                        </h6>
                    @endif
                    @if($subtitle)
                        <p class="text-muted small mb-0 mt-0.5">{{ $subtitle }}</p>
                    @endif
                </div>

                {{ $header ?? '' }}
            </div>

            <div class="d-flex align-items-center gap-2 ms-auto">
                @if($badge)
                    <span class="badge {{ $badgeClass }} small px-2.5 py-1.5 fw-medium">
                        {{ $badge }}
                    </span>
                @endif

                @if(isset($actions))
                    {{ $actions }}
                @endif
            </div>
        </div>
    @endif

    <div class="{{ $noPadding ? '' : 'card-body p-4' }} {{ $bodyClass }}">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="card-footer px-4 py-3 border-top"
             style="background-color: var(--theme-table-header-bg, #f8fafc); border-color: var(--theme-card-border, #e2e8f0);">
            {{ $footer }}
        </div>
    @endif
</div>
