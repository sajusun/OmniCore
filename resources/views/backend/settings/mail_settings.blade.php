<x-admin-layout>
    @slot('title')
    Mail Settings
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
                    Mail Settings
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
                    <span class="font-semibold">Warning:</span> Please configure the mail settings carefully. Invalid credentials will cause mail dispatching (e.g. registration, verification, password reset) to fail.
                </p>
            </div>

            <!-- Mail Configuration Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl shadow-indigo-100/30 dark:shadow-gray-900/50 border border-gray-100/50 dark:border-gray-700/50 overflow-hidden backdrop-blur-sm transition-all duration-300 hover:shadow-indigo-200/40 dark:hover:shadow-gray-700/30 mb-8">

                <!-- Card Header -->
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30 flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/40 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 dark:text-indigo-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Mail Server Configuration</h3>
                    <span
                        class="ml-auto text-xs bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 px-3 py-1 rounded-full border border-indigo-100 dark:border-indigo-800">
                        system settings
                    </span>
                </div>

                <!-- Card Body -->
                <div class="p-6 sm:p-8">
                    <form class="form form-horizontal" method="post"
                        action="{{ route('admin.setting.mail.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-4">
                                <x-form.text
                                    name="mail_mailer"
                                    label="Mail Mailer"
                                    placeholder="e.g. smtp, mailgun, ses"
                                    value="{{ $env['MAIL_MAILER'] ?? '' }}" />

                                <x-form.text
                                    name="mail_host"
                                    label="Mail Host"
                                    placeholder="e.g. smtp.mailtrap.io"
                                    value="{{ $env['MAIL_HOST'] ?? '' }}" />

                                <x-form.text
                                    name="mail_port"
                                    label="Mail Port"
                                    placeholder="e.g. 2525, 465, 587"
                                    value="{{ $env['MAIL_PORT'] ?? '' }}" />

                                <x-form.text
                                    name="mail_encryption"
                                    label="Mail Encryption"
                                    placeholder="e.g. tls, ssl"
                                    value="{{ $env['MAIL_ENCRYPTION'] ?? '' }}" />
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-4">
                                <x-form.text
                                    name="mail_username"
                                    label="Mail Username"
                                    placeholder="Enter username/email"
                                    value="{{ $env['MAIL_USERNAME'] ?? '' }}" />

                                <x-form.text
                                    name="mail_password"
                                    label="Mail Password"
                                    placeholder="Enter mail account password"
                                    value="{{ $env['MAIL_PASSWORD'] ?? '' }}" />

                                <x-form.email
                                    name="mail_from_address"
                                    label="Mail From Address"
                                    placeholder="noreply@example.com"
                                    value="{{ $env['MAIL_FROM_ADDRESS'] ?? '' }}" />

                                <x-form.text
                                    name="mail_from_name"
                                    label="Mail From Name"
                                    placeholder="e.g. Support Team"
                                    value="{{ $env['MAIL_FROM_NAME'] ?? '' }}" />
                            </div>
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
                                    Updates write directly to your <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-xs">.env</code> file
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
                                Save Mail Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Test Email Dispatching Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl shadow-indigo-100/30 dark:shadow-gray-900/50 border border-gray-100/50 dark:border-gray-700/50 overflow-hidden backdrop-blur-sm transition-all duration-300 hover:shadow-indigo-200/40 dark:hover:shadow-gray-700/30">

                <!-- Card Header -->
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30 flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 dark:bg-emerald-900/40 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600 dark:text-emerald-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Send Test Email</h3>
                    <span
                        class="ml-auto text-xs bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 px-3 py-1 rounded-full border border-emerald-100 dark:border-emerald-800">
                        test connection
                    </span>
                </div>

                <!-- Card Body -->
                <div class="p-6 sm:p-8">
                    <form class="form form-horizontal" method="post"
                        action="{{ route('admin.setting.mail.send') }}">
                        @csrf

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Left Fields -->
                            <div class="space-y-4">
                                <x-form.email
                                    name="receiver"
                                    label="Receiver Email Address"
                                    placeholder="recipient@example.com"
                                    value="{{ old('receiver') }}" />

                                <x-form.text
                                    name="subject"
                                    label="Subject"
                                    placeholder="Test Email from Dashboard"
                                    value="{{ old('subject', 'Test Connection Email') }}" />
                            </div>

                            <!-- Right Textarea -->
                            <div>
                                <x-form.textarea
                                    name="content"
                                    label="Email Body / Content"
                                    placeholder="Write your test message here..."
                                    rows="5"
                                    value="{{ old('content', 'This is a test email sent from the Master Dashboard environment settings manager.') }}" />
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 pt-6 border-t border-gray-200/60 dark:border-gray-700/60 flex flex-wrap items-center justify-between gap-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <span class="inline-flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Verify settings by sending to a valid address.
                                </span>
                            </div>
                            <button
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-medium rounded-xl shadow-lg shadow-indigo-200 dark:shadow-indigo-900/40 transition-all duration-200 hover:shadow-indigo-300/50 hover:-translate-y-0.5 focus:ring-4 focus:ring-indigo-300/50"
                                type="submit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Dispatch Test Email
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <x-modal.status />
</x-admin-layout>