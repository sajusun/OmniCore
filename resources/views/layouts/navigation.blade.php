<nav class="navbar navbar-expand-sm bg-white border-bottom shadow-sm" x-data="{ open: false }">
    <div class="container">

        <!-- Logo -->
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
            <x-application-logo style="height: 36px; width: auto;" class="fill-current text-dark" />
        </a>

        <!-- Hamburger toggler (mobile) -->
        <button class="navbar-toggler" type="button" @click="open = !open" aria-label="Toggle navigation">
            <template x-if="!open">
                <svg style="width:24px;height:24px;" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </template>
            <template x-if="open">
                <svg style="width:24px;height:24px;" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </template>
        </button>

        <!-- Desktop Nav Links + Dropdown -->
        <div class="collapse navbar-collapse" :class="open ? 'show' : ''">

            <!-- Nav Links -->
            <ul class="navbar-nav me-auto mb-2 mb-sm-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-semibold' : '' }}"
                        href="{{ route('admin.dashboard') }}">
                        {{ __('Dashboard') }}
                    </a>
                </li>
            </ul>

            <!-- Settings Dropdown -->
            <ul class="navbar-nav ms-auto align-items-sm-center">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-1" href="#"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span>{{ Auth::user()->name }}</span>
                        <svg class="fill-current" style="width:16px;height:16px;" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li>
                            <a class="dropdown-item small" href="{{ route('profile.edit') }}">
                                {{ __('Profile') }}
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item small text-start w-100"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>

        </div>

        <!-- Responsive Settings (mobile only, shown below nav) -->
        <div class="w-100 border-top mt-2 pt-2 d-sm-none" x-show="open">
            <div class="px-2 pb-2">
                <div class="fw-medium text-dark">{{ Auth::user()->name }}</div>
                <div class="small text-muted">{{ Auth::user()->email }}</div>
            </div>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link small" href="{{ route('profile.edit') }}">{{ __('Profile') }}</a>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link small text-start w-100 border-0 bg-transparent"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </li>
            </ul>
        </div>

    </div>
</nav>
