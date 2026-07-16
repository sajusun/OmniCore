<!-- Sidebar -->
<aside
    :class="{'translate-x-0': sidebarMobileOpen, '-translate-x-full': !sidebarMobileOpen, 'lg:w-64': sidebarDesktopOpen, 'lg:w-20': !sidebarDesktopOpen}"
    class="fixed inset-y-0 left-0 z-30 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 lg:static flex flex-col w-64 lg:translate-x-0 overflow-y-auto">

    <!-- Sidebar Header -->
    <div class="flex items-center h-16 px-4 border-b border-gray-200 dark:border-gray-700 whitespace-nowrap"
        :class="sidebarDesktopOpen ? 'justify-between' : 'justify-center'">
        <a href="{{ route('admin.dashboard') }}"
            class="text-xl font-bold text-indigo-600 dark:text-indigo-400 flex items-center transition-all">
            <!-- Logo from settings -->
            <img src="{{ asset(settings('logo') ?? 'default/logo.png') }}" alt="logo"
                class="w-8 h-8 object-contain flex-shrink-0" :class="!sidebarDesktopOpen ? 'lg:mx-auto' : ''">
            <!-- Full Name -->
            <span class="ml-2 font-extrabold" :class="!sidebarDesktopOpen ? 'lg:hidden' : ''">
                {{ config('app.name', 'Admin') }}
            </span>
        </a>
        <button @click="sidebarMobileOpen = false"
            class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 absolute right-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Sidebar Links -->
    <nav class="flex-1 p-4 space-y-1 overflow-x-hidden">

        <!-- Menu Group -->
        <x-sidebar.heading>Menu</x-sidebar.heading>

        <x-sidebar.link href="{{ route('admin.dashboard') }}" :active="request()->routeIs(['admin.dashboard', 'dashboard'])">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
            </x-slot>
            Dashboard
        </x-sidebar.link>

        <x-sidebar.link href="{{ route('admin.notifications.create') }}" :active="request()->routeIs('admin.notifications.*')">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                    </path>
                </svg>
            </x-slot>
            Bulk Notifications
        </x-sidebar.link>

        <x-sidebar.link href="{{ route('admin.tickets.index') }}" :active="request()->routeIs('admin.tickets.*')">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
            </x-slot>
            Support Tickets
        </x-sidebar.link>

        <x-sidebar.link href="{{ route('admin.activity-logs.index') }}" :active="request()->routeIs('admin.activity-logs.*')">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
            </x-slot>
            Activities Logs
        </x-sidebar.link>

        <!-- Access Control Group -->
        <x-sidebar.heading>Access Control</x-sidebar.heading>

        <x-sidebar.link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
            </x-slot>
            User Management
        </x-sidebar.link>

        @if(config('app.enable_role_management'))
            <x-sidebar.dropdown title="Admin Management" :active="request()->routeIs(['admin.admins.*', 'admin.roles.*', 'admin.permissions.*', 'admin.stuff.*'])">
                <x-slot name="icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                </x-slot>
                
                <x-sidebar.sub-link href="{{ route('admin.stuff.index') }}" :active="request()->routeIs(['admin.stuff.index', 'admin.admins.*'])">
                    Staff Users
                </x-sidebar.sub-link>

                <x-sidebar.sub-link href="{{ route('admin.roles.index') }}" :active="request()->routeIs('admin.roles.*')">
                    Roles
                </x-sidebar.sub-link>

                <x-sidebar.sub-link href="{{ route('admin.permissions.index') }}" :active="request()->routeIs('admin.permissions.*')">
                    Permissions
                </x-sidebar.sub-link>
            </x-sidebar.dropdown>
        @endif

        <!-- System Settings Group -->
        <x-sidebar.heading>System Settings</x-sidebar.heading>

        <x-sidebar.link href="{{ route('admin.setting.general.index') }}" :active="request()->routeIs('admin.setting.general.index')">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                    </path>
                </svg>
            </x-slot>
            General Settings
        </x-sidebar.link>

        <x-sidebar.link href="{{ route('admin.setting.general.logo') }}" :active="request()->routeIs('admin.setting.general.logo')">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <!-- Distinct Image/Logo icon -->
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
            </x-slot>
            Logo Settings
        </x-sidebar.link>

        <x-sidebar.link href="{{ route('admin.setting.general.env') }}" :active="request()->routeIs(['admin.setting.general.env', 'admin.setting.env'])">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <!-- Code/Env terminal icon -->
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
            </x-slot>
            Env Settings
        </x-sidebar.link>

        <x-sidebar.link href="{{ route('admin.setting.profile.index') }}" :active="request()->routeIs('admin.setting.profile.*')">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
            </x-slot>
            Profile Settings
        </x-sidebar.link>

        <!-- Page Manage Group -->
        <x-sidebar.heading>Page Manage</x-sidebar.heading>

        @foreach (App\Enums\PageName::cases() as $page)
            <x-sidebar.dropdown :title="$page->label()" :active="request()->route('page') === $page->value">
                <x-slot name="icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </x-slot>

                @foreach ($page->sections() as $section)
                    <x-sidebar.sub-link href="{{ route('admin.cms.page.edit', [$page->value, $section->value]) }}"
                        :active="request()->route('page') === $page->value && request()->route('section') === $section->value">
                        {{ str($section->value)->replace('-', ' ')->title() }}
                    </x-sidebar.sub-link>
                @endforeach
            </x-sidebar.dropdown>
        @endforeach

    </nav>
</aside>