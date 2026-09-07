<x-admin-layout>
    @slot('title')
    Firebase Settings
    @endslot

    @slot('header')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
        <nav class="small fw-medium text-muted d-none d-sm-flex align-items-center bg-light px-3 py-2 shadow-sm border">
            <ol class="d-flex list-unstyled m-0 gap-2">
                <li class="d-flex align-items-center">
                    <a href="javascript:void(0);" class="text-decoration-none text-secondary">Settings</a>
                    <span class="mx-2 text-muted">/</span>
                </li>
                <li class="d-flex align-items-center text-dark fw-medium" aria-current="page">
                    Firebase
                </li>
            </ol>
        </nav>
    </div>
    @endslot

    <div class="py-4">
        <div class="container-fluid">

            <!-- Warning Banner -->
            <div class="alert alert-warning d-flex align-items-start gap-3 px-4 py-3" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="flex-shrink-0 mt-1">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="small">
                    <span class="fw-bold">Warning:</span> Carefully change your Firebase credentials. Incorrect values may break notification services.
                </div>
            </div>

            <!-- Main Card -->
            <div class="card shadow-sm border overflow-hidden">

                <!-- Card Header -->
                <div class="card-header bg-light d-flex align-items-center gap-2 py-3 px-4">
                    <div class="p-2 bg-warning bg-opacity-10 text-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M5.042 13.274L3.232 4.018l4.56 4.386 1.848-6.02 4.8 11.16L19.2 7.8l.6 9.6H4.2l.842-4.126zM3 19.8h18v1.2H3v-1.2z"/>
                        </svg>
                    </div>
                    <h6 class="card-title fw-bold mb-0">Firebase Configuration</h6>
                    <span class="badge bg-warning text-dark ms-auto">sensitive</span>
                </div>

                <!-- Card Body -->
                <div class="card-body p-4">
                    <form class="form form-horizontal" method="post"
                        action="{{ route('admin.setting.firebase.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row g-3">
                            <div class="col-md-8 col-12">
                                <x-form.text
                                    name="firebase_credentials"
                                    label="Firebase Credentials"
                                    placeholder="Enter your Firebase credentials path or JSON"
                                    value="{{ env('FIREBASE_CREDENTIALS') ?? old('firebase_credentials') }}" />
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
                                    Changes are applied immediately
                                </span>
                            </div>
                            <button class="btn btn-primary d-inline-flex align-items-center px-4 py-2" type="submit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="me-2" fill="none"
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
            <div class="row g-3 mt-4">
                <div class="col-md-4">
                    <div class="card p-3 d-flex flex-row align-items-center gap-3 border shadow-sm h-100">
                        <div class="p-2 bg-warning bg-opacity-10 text-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Push Notifications</p>
                            <p class="fw-semibold mb-0">Firebase Cloud Messaging</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3 d-flex flex-row align-items-center gap-3 border shadow-sm h-100">
                        <div class="p-2 bg-primary bg-opacity-10 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Security</p>
                            <p class="fw-semibold mb-0">Service account JSON</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3 d-flex flex-row align-items-center gap-3 border shadow-sm h-100">
                        <div class="p-2 bg-success bg-opacity-10 text-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Real-time</p>
                            <p class="fw-semibold mb-0">Database & Storage</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <x-modal.status />
</x-admin-layout>