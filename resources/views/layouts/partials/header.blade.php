<!-- Top Header -->
<header
    class="flex items-center justify-between px-6 py-4 h-16 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 shadow-sm">
    <div class="flex items-center">

        <!-- Mobile Hamburger -->
        <button @click="sidebarMobileOpen = true"
            class="text-gray-500 focus:outline-none lg:hidden mr-4 hover:text-indigo-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
            </svg>
        </button>

        <!-- Desktop Hamburger / Toggle -->
        <button @click="toggleDesktop()"
            class="text-gray-500 focus:outline-none hidden lg:block mr-4 hover:text-indigo-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
        </button>


        <!-- Header Slot -->
        <div class="ml-4 lg:ml-0">
            @isset($header)
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $header }}
            </h2>
            @else
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">

                <div class="flex justify-between items-center">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Dashboard
                    </h2>
                    <nav class="text-sm font-medium text-gray-500 dark:text-gray-400 hidden sm:flex items-center">
                        <span class="mx-2"></span>
                        <span class="text-gray-700 dark:text-gray-200 font-semibold">
                            <button onclick="location.href=`{{ route('admin.optimize') }}`">
                                <i class="fas fa-sync fs-5"></i>
                            </button>
                        </span>
                    </nav>
                </div>
            </h2>

            @endisset
        </div>
    </div>

    <div class="flex items-center">
        <!-- Full Screen Toggle Button -->
        <button @click="toggleFullScreen()" class="text-gray-500 hover:text-indigo-600 focus:outline-none mr-4 transition-colors hidden sm:block">
            <template x-if="!isFullscreen">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4">
                    </path>
                </svg>
            </template>
            <template x-if="isFullscreen">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 14h4v4m0-4l-5 5m17-5h-4v4m0-4l5 5M4 10h4V6m0 4L3 5m17 5h-4V6m0 4l5-5">
                    </path>
                </svg>
            </template>
        </button>

        <!-- Theme Toggle Button -->
        <button @click="toggleTheme()" class="text-gray-500 hover:text-indigo-600 focus:outline-none mr-4 transition-colors">
            <template x-if="!isDarkMode">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                    </path>
                </svg>
            </template>
            <template x-if="isDarkMode">
                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
            </template>
        </button>

        @if (config('app.enable_in_app_notifications'))
        @include('layouts.partials.notification')
        @endif

        <!-- Profile Dropdown (Alpine.js) -->
        <div class="relative" x-data="{ dropdownOpen: false }">
            <button @click="dropdownOpen = !dropdownOpen" class="flex items-center focus:outline-none">
                <div class="w-8 h-8 overflow-hidden rounded-full border-2 border-indigo-500">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&color=7F9CF5&background=EBF4FF"
                        class="object-cover w-full h-full" alt="avatar">
                </div>
                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-200 hidden sm:block">
                    {{ Auth::user()->name ?? 'Admin' }}
                </span>
                <svg class="w-4 h-4 ml-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-100 dark:border-gray-700 py-1 z-50"
                style="display: none;">
                <a href="{{ Route::has('admin.setting.profile.index') ? route('admin.setting.profile.index') : '#' }}"
                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Profile</a>
                <form method="POST" action="{{ Route::has('logout') ? route('logout') : '#' }}">
                    @csrf
                    <button type="submit"
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>