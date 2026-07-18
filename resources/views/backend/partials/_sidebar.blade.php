@php
use Illuminate\Support\Facades\Route;
@endphp

<!--APP-SIDEBAR-->
<div class="sticky">
    <div class="app-sidebar__overlay" data-bs-toggle="sidebar"></div>
    <div class="app-sidebar" style="overflow: scroll">
        <div class="side-header">
            <a class="header-brand1" href="{{ route('admin.dashboard') }}">
                <img src="{{ asset(settings('logo') ?? 'default/logo.png') }}" id="header-brand-logo" alt="logo"
                    width="67" height="67">
            </a>
        </div>
        <div class="main-sidemenu">
            <ul class="side-menu mt-2">
                <li>
                    <h3>Menu</h3>
                </li>
                <li class="slide">
                    <a class="side-menu__item {{ request()->routeIs('dashboard') ? 'has-link active' : '' }}"
                        href="{{ route('admin.dashboard') }}">
                        <i class="fa-solid fa-house side-menu__icon"></i>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>

                <li>
                    <h3>Access Control</h3>
                </li>
                <li class="slide {{ request()->routeIs('admin.users.*') ? 'is-expanded' : '' }}">
                    <a class="side-menu__item" data-bs-toggle="slide" href="{{ route('admin.users.index') }}">
                        <i class="fa-solid fa-users side-menu__icon"></i>
                        <span class="side-menu__label">Users</span>
                    </a>
                </li>

                @if(env('ENABLE_ROLE_MANAGEMENT'))
                <li
                    class="slide {{ request()->routeIs(['admin.admins.*', 'admin.roles.*', 'admin.permissions.*']) ? 'is-expanded' : '' }}">
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
                        <i class="fa-solid fa-user-shield side-menu__icon"></i>
                        <span class="side-menu__label">Admin Management</span>
                        <i class="angle fe fe-chevron-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a href="{{ route('admin.stuff.index') }}"
                                class="slide-item {{ request()->routeIs('admin.stuff.*') ? 'active' : '' }}">Staff
                                Users</a></li>
                        <li><a href="{{ route('admin.roles.index') }}"
                                class="slide-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">Roles</a>
                        </li>
                        <li><a href="{{ route('admin.permissions.index') }}"
                                class="slide-item {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">Permissions</a>
                        </li>
                    </ul>
                </li>
                @endif
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


                {{-- end pages --}}

                <li>
                    <h3>System Settings</h3>
                </li>
                <li class="slide">
                    <a class="side-menu__item {{ request()->routeIs('admin.setting.general.index') ? 'active' : '' }}"
                        href="{{ route('admin.setting.general.index') }}">
                        <i class="fa-solid fa-cog side-menu__icon"></i>
                        <span class="side-menu__label">General Settings</span>
                    </a>
                </li>

                @if(config('app.enable_env_edit'))
                <x-sidebar.link href="{{ route('admin.setting.general.env') }}"
                    :active="request()->routeIs(['admin.setting.general.env', 'admin.setting.env'])">
                    <x-slot name="icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </x-slot>
                    Env Settings
                </x-sidebar.link>
                @endif

                <li class="slide">
                    <a class="side-menu__item {{ request()->routeIs('admin.setting.profile.index') ? 'active' : '' }}"
                        href="{{ route('admin.setting.profile.index') }}">
                        <i class="fa-solid fa-user-circle side-menu__icon"></i>
                        <span class="side-menu__label">Profile Settings</span>
                    </a>
                </li>

            </ul>
            <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24"
                    height="24" viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" />
                </svg>
            </div>
        </div>
    </div>
</div>
<!--/APP-SIDEBAR-->