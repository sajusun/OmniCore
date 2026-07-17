<x-admin-layout>
    @slot('title')
    Mail Settings
    @endslot

    @slot('header')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
        <nav class="small fw-medium text-muted d-none d-sm-flex align-items-center bg-light px-3 py-2 rounded-pill shadow-sm border">
            <ol class="d-flex list-unstyled m-0 gap-2">
                <li class="d-flex align-items-center">
                    <a href="javascript:void(0);" class="text-decoration-none text-secondary">Settings</a>
                    <span class="mx-2 text-muted">/</span>
                </li>
                <li class="d-flex align-items-center text-dark fw-medium" aria-current="page">
                    Mail Settings
                </li>
            </ol>
        </nav>
    </div>
    @endslot

    <div class="py-4">
        <div class="container-fluid">

            <!-- Warning Banner -->
            <div class="alert alert-warning d-flex align-items-start gap-3 px-4 py-3 rounded-3" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="flex-shrink-0 mt-1">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="small">
                    <span class="fw-bold">Warning:</span> Please configure the mail settings carefully. Invalid credentials will cause mail dispatching (e.g. registration, verification, password reset) to fail.
                </div>
            </div>

            <!-- Mail Configuration Card -->
            <div class="card shadow-sm border overflow-hidden mb-4">

                <!-- Card Header -->
                <div class="card-header bg-light d-flex align-items-center gap-2 py-3 px-4">
                    <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h6 class="card-title fw-bold mb-0">Mail Server Configuration</h6>
                    <span class="badge bg-primary ms-auto">system settings</span>
                </div>

                <!-- Card Body -->
                <div class="card-body p-4">
                    <form class="form form-horizontal" method="post"
                        action="{{ route('admin.setting.mail.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="row g-3">
                            <!-- Left Column -->
                            <div class="col-md-6 d-flex flex-column gap-2">
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
                            <div class="col-md-6 d-flex flex-column gap-2">
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
                        <div class="mt-4 pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div class="small text-muted">
                                <span class="d-inline-flex align-items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    Updates write directly to your <code class="bg-light text-dark px-1 rounded small font-monospace">.env</code> file
                                </span>
                            </div>
                            <button class="btn btn-primary d-inline-flex align-items-center px-4 py-2" type="submit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="me-2" fill="none"
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
            <div class="card shadow-sm border overflow-hidden">

                <!-- Card Header -->
                <div class="card-header bg-light d-flex align-items-center gap-2 py-3 px-4">
                    <div class="p-2 bg-success bg-opacity-10 text-success rounded-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </div>
                    <h6 class="card-title fw-bold mb-0">Send Test Email</h6>
                    <span class="badge bg-success ms-auto">test connection</span>
                </div>

                <!-- Card Body -->
                <div class="card-body p-4">
                    <form class="form form-horizontal" method="post"
                        action="{{ route('admin.setting.mail.send') }}">
                        @csrf

                        <div class="row g-3">
                            <!-- Left Fields -->
                            <div class="col-md-6 d-flex flex-column gap-2">
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
                            <div class="col-md-6">
                                <x-form.textarea
                                    name="content"
                                    label="Email Body / Content"
                                    placeholder="Write your test message here..."
                                    rows="5"
                                    value="{{ old('content', 'This is a test email sent from the Master Dashboard environment settings manager.') }}" />
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div class="small text-muted">
                                <span class="d-inline-flex align-items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Verify settings by sending to a valid address.
                                </span>
                            </div>
                            <button class="btn btn-primary d-inline-flex align-items-center px-4 py-2" type="submit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="me-2" fill="none"
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