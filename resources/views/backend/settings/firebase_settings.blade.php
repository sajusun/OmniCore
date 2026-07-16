<x-admin-layout>
    @slot('title')
    Firebase Settings
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
                    Firebase
                </li>
            </ol>
        </nav>
    </div>
    @endslot

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Warning Banner -->
            <div class="mb-6 flex items-start gap-3 px-5 py-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 rounded-xl text-amber-800 dark:text-amber-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 mt-0.5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-sm font-medium">
                    <span class="font-semibold">Warning:</span> Carefully change your Firebase credentials. Incorrect values may break notification services.
                </p>
            </div>

            <!-- Main Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl shadow-indigo-100/30 dark:shadow-gray-900/50 border border-gray-100/50 dark:border-gray-700/50 overflow-hidden backdrop-blur-sm transition-all duration-300 hover:shadow-indigo-200/40 dark:hover:shadow-gray-700/30">

                <!-- Card Header -->
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30 flex items-center gap-3">
                    <div class="p-2 bg-orange-100 dark:bg-orange-900/40 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-600 dark:text-orange-400"
                            viewBox="0 0 24 24" fill="currentColor">
                            <path d="M5.042 13.274L3.232 4.018l4.56 4.386 1.848-6.02 4.8 11.16L19.2 7.8l.6 9.6H4.2l.842-4.126zM3 19.8h18v1.2H3v-1.2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Firebase Configuration</h3>
                    <span
                        class="ml-auto text-xs bg-orange-50 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 px-3 py-1 rounded-full border border-orange-100 dark:border-orange-800">
                        sensitive
                    </span>
                </div>

                <!-- Card Body -->
                <div class="p-6 sm:p-8">
                    <form class="form form-horizontal" method="post"
                        action="{{ route('admin.setting.firebase.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="max-w-2xl space-y-6">
                            <x-form.text
                                name="firebase_credentials"
                                label="Firebase Credentials"
                                placeholder="Enter your Firebase credentials path or JSON"
                                value="{{ env('FIREBASE_CREDENTIALS') ?? old('firebase_credentials') }}" />
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
                                    Changes are applied immediately
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
                                Save Firebase Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg shadow-gray-100/40 dark:shadow-gray-900/30 p-4 border border-gray-100 dark:border-gray-700 flex items-center gap-3">
                    <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-600 dark:text-orange-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Push Notifications</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Firebase Cloud Messaging</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg shadow-gray-100/40 dark:shadow-gray-900/30 p-4 border border-gray-100 dark:border-gray-700 flex items-center gap-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Security</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Service account JSON</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg shadow-gray-100/40 dark:shadow-gray-900/30 p-4 border border-gray-100 dark:border-gray-700 flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600 dark:text-emerald-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Real-time</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Database & Storage</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <x-modal.status />
</x-admin-layout>