<x-admin-layout>
    @slot('title')
    Social Login Settings
    @endslot

    @slot('header')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <nav
            class="text-sm font-medium text-gray-500 dark:text-gray-400 hidden sm:flex items-center bg-white/50 dark:bg-gray-800/50 px-4 py-2 rounded-full shadow-sm border border-gray-200/50 dark:border-gray-700/50 backdrop-blur-sm">
            <ol class="flex list-none p-0 space-x-2">
                <li class="flex items-center">
                    <a href="javascript:void(0);" class="hover:text-indigo-600 transition-colors">Settings</a>
                    <span class="mx-2 text-gray-300 dark:text-gray-600">/</span>
                </li>
                <li class="flex items-center text-gray-700 dark:text-gray-200 font-medium" aria-current="page">
                    Social Login
                </li>
            </ol>
        </nav>
    </div>
    @endslot

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Main Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl shadow-indigo-100/30 dark:shadow-gray-900/50 border border-gray-100/50 dark:border-gray-700/50 overflow-hidden backdrop-blur-sm transition-all duration-300 hover:shadow-indigo-200/40 dark:hover:shadow-gray-700/30">

                <!-- Card Header -->
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30 flex items-center gap-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/40 rounded-lg">
                        <!-- Google "G" icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400"
                            viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Google OAuth Configuration</h3>
                    <span
                        class="ml-auto text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-3 py-1 rounded-full border border-blue-100 dark:border-blue-800">
                        required fields *
                    </span>
                </div>

                <!-- Card Body -->
                <div class="p-6 sm:p-8">
                    <form class="form form-horizontal" method="post"
                        action="{{ route('admin.setting.social.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <x-form.text
                                name="google_client_id"
                                label="Google Client ID"
                                placeholder="Enter your Google Client ID"
                                value="{{ env('GOOGLE_CLIENT_ID') ?? old('google_client_id') }}" />

                            <x-form.text
                                name="google_client_secret"
                                label="Google Client Secret"
                                placeholder="Enter your Google Client Secret"
                                value="{{ env('GOOGLE_CLIENT_SECRET') ?? old('google_client_secret') }}" />

                            <div class="lg:col-span-2">
                                <x-form.text
                                    name="google_redirect_uri"
                                    label="Google Redirect URI"
                                    placeholder="https://yourdomain.com/auth/google/callback"
                                    value="{{ env('GOOGLE_REDIRECT_URI') ?? old('google_redirect_uri') }}" />
                            </div>
                        </div>

                        <!-- Hint box -->
                        <div class="mt-6 flex items-start gap-3 p-4 bg-blue-50/70 dark:bg-blue-900/20 border border-blue-200/60 dark:border-blue-800/50 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 dark:text-blue-400 flex-shrink-0 mt-0.5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                Get your credentials from the
                                <a href="https://console.developers.google.com/" target="_blank"
                                    class="font-semibold underline underline-offset-2 hover:text-blue-900 dark:hover:text-blue-100 transition-colors">
                                    Google Developer Console
                                </a>. Make sure the redirect URI is added to the OAuth 2.0 authorized redirect URIs list.
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 pt-6 border-t border-gray-200/60 dark:border-gray-700/60 flex flex-wrap items-center justify-between gap-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <span class="inline-flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    All changes are saved immediately
                                </span>
                            </div>
                            <button
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-medium rounded-xl shadow-lg shadow-indigo-200 dark:shadow-indigo-900/40 transition-all duration-200 hover:shadow-indigo-300/50 hover:-translate-y-0.5 focus:ring-4 focus:ring-indigo-300/50"
                                type="submit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Save Social Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg shadow-gray-100/40 dark:shadow-gray-900/30 p-4 border border-gray-100 dark:border-gray-700 flex items-center gap-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">OAuth 2.0</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Google Sign-In</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg shadow-gray-100/40 dark:shadow-gray-900/30 p-4 border border-gray-100 dark:border-gray-700 flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600 dark:text-emerald-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Security</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Encrypted credentials</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg shadow-gray-100/40 dark:shadow-gray-900/30 p-4 border border-gray-100 dark:border-gray-700 flex items-center gap-3">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600 dark:text-purple-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Redirect URI</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Callback endpoint</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <x-modal.status />
</x-admin-layout>