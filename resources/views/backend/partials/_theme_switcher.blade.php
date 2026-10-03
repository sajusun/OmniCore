@php
    $activeTheme = config('theme.active', env('APP_THEME', 'modern_indigo'));
    $allThemes = config('theme.themes', []);
    $currentThemeData = $allThemes[$activeTheme] ?? [
        'name' => 'Modern Indigo',
        'accent' => '#6366f1',
        'icon' => 'fe fe-layers',
        'category' => 'Modern SaaS',
    ];
@endphp

<!-- THEME SELECTOR DROPDOWN -->
<div class="dropdown d-flex align-items-center me-2">
    <a class="nav-link leading-none d-flex align-items-center p-1" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false" title="Switch UI Theme">
        <span class="theme-indicator-badge d-none d-sm-inline-flex" style="color: {{ $currentThemeData['accent'] }}; border-color: {{ $currentThemeData['accent'] }}40; background: {{ $currentThemeData['accent'] }}15;">
            <span class="theme-dot" style="background-color: {{ $currentThemeData['accent'] }}; box-shadow: 0 0 6px {{ $currentThemeData['accent'] }};"></span>
            <i class="{{ $currentThemeData['icon'] }} me-1 fs-12"></i>
            <span id="current-theme-label">{{ $currentThemeData['name'] }}</span>
            <i class="fe fe-chevron-down fs-10 ms-1 opacity-75"></i>
        </span>
        <span class="d-inline-flex d-sm-none btn btn-sm btn-icon" style="color: {{ $currentThemeData['accent'] }};">
            <i class="{{ $currentThemeData['icon'] }} fs-16"></i>
        </span>
    </a>

    <div class="dropdown-menu dropdown-menu-end shadow-lg py-2" style="width: 290px; border-radius: 12px; z-index: 1060;" id="theme-dropdown-menu">
        <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0 fw-bold fs-13">Dashboard Themes</h6>
                <small class="text-muted fs-11">Controlled via <code class="text-primary">APP_THEME</code> in .env</small>
            </div>
            <span class="badge bg-light text-dark fs-10 px-2 py-1 border">5 Themes</span>
        </div>

        <div class="py-1">
            @foreach($allThemes as $themeKey => $theme)
                @php
                    $isThemeActive = ($themeKey === $activeTheme);
                @endphp
                <a href="javascript:void(0);"
                   class="dropdown-item d-flex align-items-center justify-content-between px-3 py-2 theme-switch-btn {{ $isThemeActive ? 'active' : '' }}"
                   data-theme-key="{{ $themeKey }}"
                   data-theme-name="{{ $theme['name'] }}"
                   data-theme-accent="{{ $theme['accent'] }}"
                   data-theme-icon="{{ $theme['icon'] }}"
                   onclick="applyThemePreview('{{ $themeKey }}', '{{ $theme['name'] }}', '{{ $theme['accent'] }}', '{{ $theme['icon'] }}')">
                    <div class="d-flex align-items-center">
                        <span class="d-inline-flex align-items-center justify-content-center me-2"
                              style="width: 26px; height: 26px; border-radius: 6px; background-color: {{ $theme['accent'] }}20; color: {{ $theme['accent'] }};">
                            <i class="{{ $theme['icon'] }} fs-13"></i>
                        </span>
                        <div>
                            <div class="fw-semibold fs-12 text-dark">{{ $theme['name'] }}</div>
                            <div class="text-muted fs-11">{{ $theme['category'] }}</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        @if($isThemeActive)
                            <span class="badge bg-success-subtle text-success fs-10 fw-semibold px-2 py-0 border border-success-subtle active-env-badge" data-key="{{ $themeKey }}">.env</span>
                        @endif
                        <span class="theme-check-icon ms-2 {{ $isThemeActive ? '' : 'd-none' }}" id="check-{{ $themeKey }}">
                            <i class="fe fe-check text-primary fw-bold"></i>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="px-3 pt-2 pb-1 border-top mt-1 bg-light rounded-bottom text-muted fs-11">
            <i class="fe fe-terminal me-1"></i>CLI: <code>php artisan theme:set {key}</code>
        </div>
    </div>
</div>

<script>
    function applyThemePreview(themeKey, themeName, themeAccent, themeIcon) {
        // Set data-theme and body class dynamically
        document.documentElement.setAttribute('data-theme', themeKey);

        const body = document.body;
        // Remove existing theme-* classes
        const classes = body.className.split(' ').filter(c => !c.startsWith('theme-'));
        classes.push('theme-' + themeKey);
        body.className = classes.join(' ');

        // Update indicator in header
        const label = document.getElementById('current-theme-label');
        if (label) label.textContent = themeName;

        const badge = document.querySelector('.theme-indicator-badge');
        if (badge) {
            badge.style.color = themeAccent;
            badge.style.borderColor = themeAccent + '40';
            badge.style.background = themeAccent + '15';
            const dot = badge.querySelector('.theme-dot');
            if (dot) {
                dot.style.backgroundColor = themeAccent;
                dot.style.boxShadow = '0 0 6px ' + themeAccent;
            }
            const icon = badge.querySelector('i');
            if (icon) {
                icon.className = themeIcon + ' me-1 fs-12';
            }
        }

        // Update check icons in dropdown
        document.querySelectorAll('.theme-check-icon').forEach(el => el.classList.add('d-none'));
        const activeCheck = document.getElementById('check-' + themeKey);
        if (activeCheck) activeCheck.classList.remove('d-none');

        // Store preview in localStorage so it persists across local page navigations
        localStorage.setItem('omnicore_preview_theme', themeKey);
    }

    // Restore preview theme if user selected one previously in browser
    document.addEventListener('DOMContentLoaded', function() {
        const storedTheme = localStorage.getItem('omnicore_preview_theme');
        if (storedTheme) {
            const btn = document.querySelector(`.theme-switch-btn[data-theme-key="${storedTheme}"]`);
            if (btn) {
                applyThemePreview(
                    storedTheme,
                    btn.dataset.themeName,
                    btn.dataset.themeAccent,
                    btn.dataset.themeIcon
                );
            }
        }
    });
</script>
