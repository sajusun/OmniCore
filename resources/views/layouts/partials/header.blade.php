<!-- Top Header -->
<header class="d-flex align-items-center justify-content-between px-4 bg-white border-bottom shadow-sm"
    style="height: 64px;">
    <div class="d-flex align-items-center">

        <!-- Mobile Hamburger -->
        <button @click="sidebarMobileOpen = true"
            class="btn btn-link text-muted p-0 me-3 d-lg-none">
            <svg style="width:24px;height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
            </svg>
        </button>

        <!-- Desktop Hamburger / Toggle -->
        <button @click="toggleDesktop()"
            class="btn btn-link text-muted p-0 me-3 d-none d-lg-block">
            <svg style="width:24px;height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
        </button>

        <!-- Header Slot -->
        <div class="ms-2 ms-lg-0">
            @isset($header)
            <h5 class="fw-semibold mb-0 text-dark">{{ $header }}</h5>
            @else
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-semibold mb-0 text-dark">Dashboard</h5>
                <nav class="d-none d-sm-flex align-items-center ms-3 small fw-medium text-muted">
                    <button class="btn btn-link text-muted p-0" onclick="location.href=`{{ route('admin.optimize') }}`">
                        <i class="fas fa-sync fs-5"></i>
                    </button>
                </nav>
            </div>
            @endisset
        </div>
    </div>

    <div class="d-flex align-items-center gap-2">

        <!-- Full Screen Toggle Button -->
        <button @click="toggleFullScreen()" class="btn btn-link text-muted p-0 d-none d-sm-block">
            <template x-if="!isFullscreen">
                <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4">
                    </path>
                </svg>
            </template>
            <template x-if="isFullscreen">
                <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 14h4v4m0-4l-5 5m17-5h-4v4m0-4l5 5M4 10h4V6m0 4L3 5m17 5h-4V6m0 4l5-5">
                    </path>
                </svg>
            </template>
        </button>

        <!-- Theme Toggle Button -->
        <button @click="toggleTheme()" class="btn btn-link text-muted p-0">
            <template x-if="!isDarkMode">
                <svg style="width:24px;height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                    </path>
                </svg>
            </template>
            <template x-if="isDarkMode">
                <svg style="width:24px;height:24px;" class="text-warning" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
            </template>
        </button>

        @if (config('app.enable_in_app_notifications'))
        @include('layouts.partials.notification')
        @endif

        <!-- Profile Dropdown -->
        <div class="dropdown" x-data="{ dropdownOpen: false }">
            <button @click="dropdownOpen = !dropdownOpen"
                class="btn btn-link text-decoration-none p-0 d-flex align-items-center">
                <div class="rounded-circle overflow-hidden border border-2 border-primary"
                    style="width:32px;height:32px;">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&color=7F9CF5&background=EBF4FF"
                        class="w-100 h-100 object-fit-cover" alt="avatar">
                </div>
                <span class="ms-2 small fw-medium text-dark d-none d-sm-block">
                    {{ Auth::user()->name ?? 'Admin' }}
                </span>
                <svg style="width:16px;height:16px;" class="ms-1 text-muted" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                class="dropdown-menu dropdown-menu-end shadow border mt-2 show"
                style="display: none; min-width: 180px;" x-cloak>
                <a href="{{ Route::has('admin.setting.profile.index') ? route('admin.setting.profile.index') : '#' }}"
                    class="dropdown-item small">Profile</a>
                <form method="POST" action="{{ Route::has('logout') ? route('logout') : '#' }}">
                    @csrf
                    <button type="submit" class="dropdown-item small text-start w-100">Logout</button>
                </form>
            </div>
        </div>

    </div>
</header>