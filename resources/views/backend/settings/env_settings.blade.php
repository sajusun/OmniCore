<x-admin-layout>
    @slot('title')
        Environment Settings
    @endslot

    @slot('header')
        <div class="flex flex-row justify-between items-center gap-4 w-full">
            <nav
                class="text-sm font-medium text-gray-500 dark:text-gray-400 hidden sm:flex items-center bg-white/50 dark:bg-gray-800/50 px-4 py-2 rounded-full shadow-sm border border-gray-200/50 dark:border-gray-700/50 backdrop-blur-sm">
                <ol class="flex list-none p-0 space-x-2">
                    <li class="flex items-center">
                        <a href="javascript:void(0);" class="hover:text-indigo-600 transition-colors">Settings</a>
                        <span class="mx-2 text-gray-300 dark:text-gray-600">/</span>
                    </li>
                    <li class="flex items-center text-gray-700 dark:text-gray-200 font-medium" aria-current="page">
                        Environment
                    </li>
                </ol>
            </nav>
             <span class="mx-2"></span>
                <span class="text-gray-700 dark:text-gray-200 font-semibold">
                    <button onclick="location.href=`{{ route('admin.optimize') }}`">
                    <i class="fas fa-sync fs-5"></i>
                </button>
                </span>
        </div>
    @endslot

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Danger Warning --}}
            <div class="mb-6 flex items-start gap-3 px-5 py-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 rounded-xl text-red-800 dark:text-red-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-sm font-medium">
                    <span class="font-semibold">Danger Zone:</span> Modifying environment variables directly updates the
                    <code class="bg-red-100 dark:bg-red-900/40 px-1.5 py-0.5 rounded text-xs font-mono">.env</code> file.
                    Changes take effect after config cache clear. Proceed with caution.
                </p>
            </div>

            {{-- Tab Panel --}}
            <div x-data="{ activeTab: '{{ session('_env_tab', 'app') }}' }" class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl shadow-indigo-100/30 dark:shadow-gray-900/50 border border-gray-100/50 dark:border-gray-700/50 overflow-hidden">

                {{-- Tab Navigation --}}
                <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30 overflow-x-auto">
                    <div class="flex min-w-max px-6 pt-4 gap-1">

                        @php
                            $tabs = [
                                ['key' => 'app',          'label' => 'Application', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',   'color' => 'indigo'],
                                ['key' => 'jwt',          'label' => 'JWT',         'icon' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z',                       'color' => 'violet'],
                                ['key' => 'firebase',     'label' => 'Firebase',    'icon' => 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z',    'color' => 'orange'],
                                ['key' => 'mail',         'label' => 'Mail',        'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',                                            'color' => 'blue'],
                                ['key' => 'verification', 'label' => 'Verification','icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'emerald'],
                                ['key' => 'system',       'label' => 'System',      'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'color' => 'rose'],
                            ];
                        @endphp

                        @foreach ($tabs as $tab)
                            @php
                                $colorMap = [
                                    'indigo'  => 'border-indigo-500 text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-900/20',
                                    'violet'  => 'border-violet-500 text-violet-600 dark:text-violet-400 bg-violet-50/50 dark:bg-violet-900/20',
                                    'orange'  => 'border-orange-500 text-orange-600 dark:text-orange-400 bg-orange-50/50 dark:bg-orange-900/20',
                                    'blue'    => 'border-blue-500 text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/20',
                                    'emerald' => 'border-emerald-500 text-emerald-600 dark:text-emerald-400 bg-emerald-50/50 dark:bg-emerald-900/20',
                                    'rose'    => 'border-rose-500 text-rose-600 dark:text-rose-400 bg-rose-50/50 dark:bg-rose-900/20',
                                ];
                                $activeClass   = $colorMap[$tab['color']];
                                $inactiveClass = 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600';
                            @endphp
                            <button
                                type="button"
                                id="env-tab-{{ $tab['key'] }}"
                                @click="activeTab = '{{ $tab['key'] }}'"
                                :class="activeTab === '{{ $tab['key'] }}' ? '{{ $activeClass }} border-b-2' : '{{ $inactiveClass }} border-b-2'"
                                class="flex items-center gap-2 px-4 py-3 text-sm font-medium rounded-t-lg transition-all duration-200 whitespace-nowrap">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $tab['icon'] }}" />
                                </svg>
                                {{ $tab['label'] }}
                            </button>
                        @endforeach

                    </div>
                </div>

                {{-- Tab Content --}}
                <div class="p-6 sm:p-8">

                    {{-- ═══════════════ TAB: Application ═══════════════ --}}
                    <div x-show="activeTab === 'app'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="mb-6">
                            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200">Application Settings</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Core application identity and URL configuration.</p>
                        </div>
                        <form method="POST" action="{{ route('admin.setting.general.env.app.update') }}">
                            @csrf
                            <input type="hidden" name="_env_tab" value="app">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <x-form.text name="app_name" label="App Name" placeholder="My Application"
                                    value="{{ $env['APP_NAME'] ?? '' }}" />

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Environment</label>
                                    <select name="app_env" class="block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                                        @foreach (['local', 'staging', 'production'] as $env_val)
                                            <option value="{{ $env_val }}" {{ ($env['APP_ENV'] ?? '') === $env_val ? 'selected' : '' }}>
                                                {{ ucfirst($env_val) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <x-form.text name="app_url" label="App URL" placeholder="https://yourdomain.com"
                                    value="{{ $env['APP_URL'] ?? '' }}" />

                                <x-form.text name="app_timezone" label="Timezone" placeholder="UTC"
                                    value="{{ $env['APP_TIMEZONE'] ?? 'UTC' }}" />

                                <x-form.text name="support_mail" label="Support Email" placeholder="support@example.com"
                                    value="{{ $env['SUPPORT_MAIL'] ?? '' }}" />

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Debug Mode</label>
                                    <select name="app_debug" class="block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                                        <option value="false" {{ ($env['APP_DEBUG'] ?? 'false') === 'false' ? 'selected' : '' }}>Disabled (Production)</option>
                                        <option value="true"  {{ ($env['APP_DEBUG'] ?? '') === 'true'  ? 'selected' : '' }}>Enabled (Development)</option>
                                    </select>
                                </div>

                                <x-form.text name="frontend_url" label="Frontend URL" placeholder="https://app.yourdomain.com"
                                    value="{{ $env['FRONTEND_URL'] ?? '' }}" />

                                <x-form.text name="backend_url" label="Backend / Admin URL" placeholder="https://yourdomain.com/admin"
                                    value="{{ $env['BACKEND_URL'] ?? '' }}" />
                            </div>
                            @include('backend.settings.partials.env-save-footer', ['label' => 'Save Application Settings'])
                        </form>
                    </div>

                    {{-- ═══════════════ TAB: JWT ═══════════════ --}}
                    <div x-show="activeTab === 'jwt'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="mb-6">
                            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200">JWT Token Settings</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure API authentication token lifetimes and security options.</p>
                        </div>
                        <div class="mb-5 flex items-start gap-3 px-4 py-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 rounded-xl text-amber-700 dark:text-amber-300 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>JWT_TTL is in <strong>minutes</strong>. Default 43200 = 30 days. JWT_SECRET should only be changed via <code class="font-mono bg-amber-100 dark:bg-amber-900/40 px-1 rounded">php artisan jwt:secret</code>.</span>
                        </div>
                        <form method="POST" action="{{ route('admin.setting.general.env.jwt.update') }}">
                            @csrf
                            <input type="hidden" name="_env_tab" value="jwt">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                        JWT Secret
                                        <span class="ml-2 text-xs font-normal text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-2 py-0.5 rounded-full">sensitive</span>
                                    </label>
                                    <input type="text" name="jwt_secret"
                                        class="block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm font-mono focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all"
                                        value="{{ $env['JWT_SECRET'] ?? '' }}"
                                        placeholder="Generate via: php artisan jwt:secret" />
                                </div>

                                <x-form.text name="jwt_ttl" label="Token TTL (minutes)" placeholder="43200"
                                    value="{{ $env['JWT_TTL'] ?? '43200' }}" />

                                <x-form.text name="jwt_refresh_ttl" label="Refresh Token TTL (minutes)" placeholder="20160"
                                    value="{{ $env['JWT_REFRESH_TTL'] ?? '20160' }}" />

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Algorithm</label>
                                    <select name="jwt_algo" class="block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                                        @foreach (['HS256', 'HS384', 'HS512', 'RS256', 'RS384', 'RS512', 'ES256', 'ES384', 'ES512'] as $algo)
                                            <option value="{{ $algo }}" {{ ($env['JWT_ALGO'] ?? 'HS256') === $algo ? 'selected' : '' }}>{{ $algo }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Blacklist Enabled</label>
                                    <select name="jwt_blacklist_enabled" class="block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                                        <option value="true"  {{ ($env['JWT_BLACKLIST_ENABLED'] ?? 'true') === 'true'  ? 'selected' : '' }}>Enabled</option>
                                        <option value="false" {{ ($env['JWT_BLACKLIST_ENABLED'] ?? '') === 'false' ? 'selected' : '' }}>Disabled</option>
                                    </select>
                                </div>

                                <x-form.text name="jwt_blacklist_grace_period" label="Blacklist Grace Period (seconds)" placeholder="0"
                                    value="{{ $env['JWT_BLACKLIST_GRACE_PERIOD'] ?? '0' }}" />

                                <x-form.text name="jwt_leeway" label="Leeway (seconds)" placeholder="0"
                                    value="{{ $env['JWT_LEEWAY'] ?? '0' }}" />
                            </div>
                            @include('backend.settings.partials.env-save-footer', ['label' => 'Save JWT Settings'])
                        </form>
                    </div>

                    {{-- ═══════════════ TAB: Firebase ═══════════════ --}}
                    <div x-show="activeTab === 'firebase'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="mb-6">
                            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200">Firebase Settings</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure Firebase project for push notifications and realtime database.</p>
                        </div>
                        <div class="mb-5 flex items-start gap-3 px-4 py-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/50 rounded-xl text-blue-700 dark:text-blue-300 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Download your Service Account JSON from <a href="https://console.firebase.google.com" target="_blank" class="underline">Firebase Console</a> → Project Settings → Service accounts, then set the file path in <strong>FIREBASE_CREDENTIALS</strong>.</span>
                        </div>
                        <form method="POST" action="{{ route('admin.setting.general.env.firebase.update') }}">
                            @csrf
                            <input type="hidden" name="_env_tab" value="firebase">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <x-form.text name="firebase_project" label="Firebase Project ID" placeholder="my-project-id"
                                    value="{{ $env['FIREBASE_PROJECT'] ?? 'app' }}" />

                                <x-form.text name="firebase_storage_default_bucket" label="Default Storage Bucket" placeholder="my-project.appspot.com"
                                    value="{{ $env['FIREBASE_STORAGE_DEFAULT_BUCKET'] ?? '' }}" />

                                <x-form.text name="firebase_database_url" label="Realtime Database URL" placeholder="https://my-project.firebaseio.com"
                                    value="{{ $env['FIREBASE_DATABASE_URL'] ?? '' }}" />

                                <div class="lg:col-span-2">
                                    <x-form.text name="firebase_credentials" label="Service Account JSON Path" placeholder="storage/app/firebase-credentials.json"
                                        value="{{ $env['FIREBASE_CREDENTIALS'] ?? '' }}" />
                                </div>
                            </div>
                            @include('backend.settings.partials.env-save-footer', ['label' => 'Save Firebase Settings'])
                        </form>
                    </div>

                    {{-- ═══════════════ TAB: Mail ═══════════════ --}}
                    <div x-show="activeTab === 'mail'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="mb-6">
                            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200">Mail / SMTP Settings</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure your outgoing mail server (SMTP) credentials.</p>
                        </div>
                        <form method="POST" action="{{ route('admin.setting.general.env.mail.update') }}">
                            @csrf
                            <input type="hidden" name="_env_tab" value="mail">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Mail Mailer</label>
                                    <select name="mail_mailer" class="block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                        @foreach (['smtp', 'sendmail', 'mailgun', 'ses', 'postmark', 'resend', 'log', 'array'] as $mailer)
                                            <option value="{{ $mailer }}" {{ ($env['MAIL_MAILER'] ?? 'log') === $mailer ? 'selected' : '' }}>{{ strtoupper($mailer) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Encryption Scheme</label>
                                    <select name="mail_scheme" class="block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                        <option value="null"  {{ in_array($env['MAIL_SCHEME'] ?? 'null', ['null', '']) ? 'selected' : '' }}>None</option>
                                        <option value="tls"   {{ ($env['MAIL_SCHEME'] ?? '') === 'tls'  ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl"   {{ ($env['MAIL_SCHEME'] ?? '') === 'ssl'  ? 'selected' : '' }}>SSL</option>
                                    </select>
                                </div>

                                <x-form.text name="mail_host" label="SMTP Host" placeholder="smtp.mailtrap.io"
                                    value="{{ $env['MAIL_HOST'] ?? '127.0.0.1' }}" />

                                <x-form.text name="mail_port" label="SMTP Port" placeholder="587"
                                    value="{{ $env['MAIL_PORT'] ?? '2525' }}" />

                                <x-form.text name="mail_username" label="SMTP Username" placeholder="username"
                                    value="{{ $env['MAIL_USERNAME'] ?? '' }}" />

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">SMTP Password</label>
                                    <input type="password" name="mail_password"
                                        class="block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                        value="{{ $env['MAIL_PASSWORD'] ?? '' }}"
                                        placeholder="••••••••" />
                                </div>

                                <x-form.text name="mail_from_address" label="From Address" placeholder="hello@example.com"
                                    value="{{ $env['MAIL_FROM_ADDRESS'] ?? '' }}" />

                                <x-form.text name="mail_from_name" label="From Name" placeholder="My Application"
                                    value="{{ $env['MAIL_FROM_NAME'] ?? '' }}" />
                            </div>
                            @include('backend.settings.partials.env-save-footer', ['label' => 'Save Mail Settings'])
                        </form>
                    </div>

                    {{-- ═══════════════ TAB: Verification ═══════════════ --}}
                    <div x-show="activeTab === 'verification'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="mb-6">
                            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200">Verification Settings</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">OTP and token-based user verification configuration.</p>
                        </div>
                        <form method="POST" action="{{ route('admin.setting.general.env.verification.update') }}">
                            @csrf
                            <input type="hidden" name="_env_tab" value="verification">

                            <div class="mb-6 pb-6 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-4">General</p>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Default Verification Type</label>
                                        <select name="verification_default_type" class="block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                                            <option value="otp"   {{ ($env['VERIFICATION_DEFAULT_TYPE'] ?? 'otp') === 'otp'   ? 'selected' : '' }}>OTP</option>
                                            <option value="token" {{ ($env['VERIFICATION_DEFAULT_TYPE'] ?? '') === 'token' ? 'selected' : '' }}>Token</option>
                                        </select>
                                    </div>

                                    <x-form.text name="verification_max_attempts" label="Max Attempts" placeholder="5"
                                        value="{{ $env['VERIFICATION_MAX_ATTEMPTS'] ?? '5' }}" />
                                </div>
                            </div>

                            <div class="mb-6 pb-6 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-4">OTP Settings</p>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <x-form.text name="verification_otp_digits" label="OTP Digits (4–8)" placeholder="6"
                                        value="{{ $env['VERIFICATION_OTP_DIGITS'] ?? '6' }}" />
                                    <x-form.text name="verification_otp_expiry_minutes" label="OTP Expiry (minutes)" placeholder="10"
                                        value="{{ $env['VERIFICATION_OTP_EXPIRY_MINUTES'] ?? '10' }}" />
                                </div>
                            </div>

                            <div class="mb-6 pb-6 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-4">Token Settings</p>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <x-form.text name="verification_token_length" label="Token Length (16–128)" placeholder="64"
                                        value="{{ $env['VERIFICATION_TOKEN_LENGTH'] ?? '64' }}" />
                                    <x-form.text name="verification_token_expiry_minutes" label="Token Expiry (minutes)" placeholder="10"
                                        value="{{ $env['VERIFICATION_TOKEN_EXPIRY_MINUTES'] ?? '10' }}" />
                                </div>
                            </div>

                            <div class="mb-6 pb-6 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-4">Resend & Block Rules</p>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <x-form.text name="verification_resend_cooldown_seconds" label="Resend Cooldown (seconds)" placeholder="60"
                                        value="{{ $env['VERIFICATION_RESEND_COOLDOWN_SECONDS'] ?? '60' }}" />
                                    <x-form.text name="verification_max_resend_requests" label="Max Resend Requests" placeholder="5"
                                        value="{{ $env['VERIFICATION_MAX_RESEND_REQUESTS'] ?? '5' }}" />
                                    <x-form.text name="verification_block_hours" label="Block Duration (hours)" placeholder="24"
                                        value="{{ $env['VERIFICATION_BLOCK_HOURS'] ?? '24' }}" />
                                </div>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-4">Redirect URLs</p>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <x-form.text name="verification_success_redirect_url" label="Success Redirect URL" placeholder="/verification/success"
                                        value="{{ $env['VERIFICATION_SUCCESS_REDIRECT_URL'] ?? '/verification/success' }}" />
                                    <x-form.text name="verification_failed_redirect_url" label="Failed Redirect URL" placeholder="/verification/failed"
                                        value="{{ $env['VERIFICATION_FAILED_REDIRECT_URL'] ?? '/verification/failed' }}" />
                                </div>
                            </div>

                            @include('backend.settings.partials.env-save-footer', ['label' => 'Save Verification Settings'])
                        </form>
                    </div>

                    {{-- ═══════════════ TAB: System ═══════════════ --}}
                    <div x-show="activeTab === 'system'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="mb-6">
                            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200">System Feature Toggles</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Enable or disable system-wide features and notification channels.</p>
                        </div>
                        <form method="POST" action="{{ route('admin.setting.general.env.system.update') }}">
                            @csrf
                            <input type="hidden" name="_env_tab" value="system">

                            {{-- Feature Flags --}}
                            <div class="mb-6 pb-6 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-4">Feature Flags</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @php
                                        $toggles = [
                                            ['key' => 'ENABLE_ROLE_MANAGEMENT', 'name' => 'enable_role_management', 'label' => 'Role Management', 'vals' => ['true','false']],
                                            ['key' => 'IN_APP_NOTIFICATIONS',   'name' => 'in_app_notifications',   'label' => 'In-App Notifications', 'vals' => ['true','false']],
                                        ];
                                    @endphp
                                    @foreach ($toggles as $toggle)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600/50">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $toggle['label'] }}</span>
                                            <select name="{{ $toggle['name'] }}" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-2 py-1.5 focus:ring-2 focus:ring-rose-500 focus:border-transparent">
                                                <option value="true"  {{ ($env[$toggle['key']] ?? 'false') === 'true'  ? 'selected' : '' }}>Enabled</option>
                                                <option value="false" {{ ($env[$toggle['key']] ?? 'false') === 'false' ? 'selected' : '' }}>Disabled</option>
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Service Toggles --}}
                            <div class="mb-6 pb-6 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-4">Service Channels</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @php
                                        $serviceToggles = [
                                            ['key' => 'SMS',            'name' => 'sms',    'label' => 'SMS Service',      'on' => 'on',  'off' => 'off'],
                                            ['key' => 'MAIL',           'name' => 'mail',   'label' => 'Mail Service',     'on' => 'on',  'off' => 'off'],
                                            ['key' => 'REVERB',         'name' => 'reverb', 'label' => 'WebSocket (Reverb)','on' => 'on', 'off' => 'off'],
                                            ['key' => 'RECAPTCHA_ENABLE','name' => 'recaptcha_enable', 'label' => 'reCAPTCHA', 'on' => 'yes', 'off' => 'no'],
                                        ];
                                    @endphp
                                    @foreach ($serviceToggles as $st)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600/50">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $st['label'] }}</span>
                                            <select name="{{ $st['name'] }}" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-2 py-1.5 focus:ring-2 focus:ring-rose-500 focus:border-transparent">
                                                <option value="{{ $st['on'] }}"  {{ ($env[$st['key']] ?? $st['off']) === $st['on']  ? 'selected' : '' }}>{{ ucfirst($st['on']) }}</option>
                                                <option value="{{ $st['off'] }}" {{ ($env[$st['key']] ?? $st['off']) === $st['off'] ? 'selected' : '' }}>{{ ucfirst($st['off']) }}</option>
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Notification Channels --}}
                            <div class="mb-6 pb-6 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-4">Notification Channels</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                    @php
                                        $notifChannels = [
                                            ['key' => 'NOTIFICATION_DATABASE',  'name' => 'notification_database',  'label' => 'Database'],
                                            ['key' => 'NOTIFICATION_FIREBASE',  'name' => 'notification_firebase',  'label' => 'Firebase'],
                                            ['key' => 'NOTIFICATION_BROADCAST', 'name' => 'notification_broadcast', 'label' => 'Broadcast'],
                                            ['key' => 'NOTIFICATION_MAIL',      'name' => 'notification_mail',      'label' => 'Mail'],
                                        ];
                                    @endphp
                                    @foreach ($notifChannels as $nc)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600/50">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $nc['label'] }}</span>
                                            <select name="{{ $nc['name'] }}" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-2 py-1.5 focus:ring-2 focus:ring-rose-500 focus:border-transparent">
                                                <option value="true"  {{ ($env[$nc['key']] ?? 'false') === 'true'  ? 'selected' : '' }}>Enabled</option>
                                                <option value="false" {{ ($env[$nc['key']] ?? 'false') === 'false' ? 'selected' : '' }}>Disabled</option>
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Misc --}}
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-4">Miscellaneous</p>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <x-form.text name="pagination" label="Default Pagination (items/page)" placeholder="12"
                                        value="{{ $env['PAGINATION'] ?? '12' }}" />
                                    <x-form.text name="google_maps_api_key" label="Google Maps API Key" placeholder="AIzaSy..."
                                        value="{{ $env['GOOGLE_MAPS_API_KEY'] ?? '' }}" />
                                </div>
                            </div>

                            @include('backend.settings.partials.env-save-footer', ['label' => 'Save System Settings'])
                        </form>
                    </div>

                </div>{{-- /tab content --}}
            </div>{{-- /alpine --}}

            {{-- Info Cards --}}
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg shadow-gray-100/40 dark:shadow-gray-900/30 p-4 border border-gray-100 dark:border-gray-700 flex items-center gap-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Config file</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">.env application settings</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg shadow-gray-100/40 dark:shadow-gray-900/30 p-4 border border-gray-100 dark:border-gray-700 flex items-center gap-3">
                    <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Risk level</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">High — use with care</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg shadow-gray-100/40 dark:shadow-gray-900/30 p-4 border border-gray-100 dark:border-gray-700 flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Effect</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Requires config:clear</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <x-modal.status />
</x-admin-layout>