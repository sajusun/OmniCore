<x-admin-layout>
    @slot('title')
    Social Login Settings
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
                    Social Login
                </li>
            </ol>
        </nav>
    </div>
    @endslot

    <div class="py-4">
        <div class="container-fluid">

            <!-- Main Card -->
            <div class="card shadow-sm border overflow-hidden">

                <!-- Card Header -->
                <div class="card-header bg-light d-flex align-items-center gap-2 py-3 px-4">
                    <div class="p-2 bg-primary bg-opacity-10 text-primary">
                        <!-- Google "G" icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                    </div>
                    <h6 class="card-title fw-bold mb-0">Google OAuth Configuration</h6>
                    <span class="badge bg-primary ms-auto">required fields *</span>
                </div>

                <!-- Card Body -->
                <div class="card-body p-4">
                    <form class="form form-horizontal" method="post"
                        action="{{ route('admin.setting.social.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-form.text
                                    name="google_client_id"
                                    label="Google Client ID"
                                    placeholder="Enter your Google Client ID"
                                    value="{{ env('GOOGLE_CLIENT_ID') ?? old('google_client_id') }}" />
                            </div>

                            <div class="col-md-6">
                                <x-form.text
                                    name="google_client_secret"
                                    label="Google Client Secret"
                                    placeholder="Enter your Google Client Secret"
                                    value="{{ env('GOOGLE_CLIENT_SECRET') ?? old('google_client_secret') }}" />
                            </div>

                            <div class="col-12">
                                <x-form.text
                                    name="google_redirect_uri"
                                    label="Google Redirect URI"
                                    placeholder="https://yourdomain.com/auth/google/callback"
                                    value="{{ env('GOOGLE_REDIRECT_URI') ?? old('google_redirect_uri') }}" />
                            </div>
                        </div>

                        <!-- Hint box -->
                        <div class="mt-4 p-3 bg-light border d-flex align-items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="text-primary flex-shrink-0 mt-1"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="small text-muted mb-0">
                                Get your credentials from the
                                <a href="https://console.developers.google.com/" target="_blank"
                                    class="fw-semibold text-primary text-decoration-none">
                                    Google Developer Console
                                </a>. Make sure the redirect URI is added to the OAuth 2.0 authorized redirect URIs list.
                            </p>
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
                                    All changes are saved immediately
                                </span>
                            </div>
                            <button class="btn btn-primary d-inline-flex align-items-center px-4 py-2" type="submit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="me-2" fill="none"
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
            <div class="row g-3 mt-4">
                <div class="col-md-4">
                    <div class="card p-3 d-flex flex-row align-items-center gap-3 border shadow-sm h-100">
                        <div class="p-2 bg-primary bg-opacity-10 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">OAuth 2.0</p>
                            <p class="fw-semibold mb-0">Google Sign-In</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3 d-flex flex-row align-items-center gap-3 border shadow-sm h-100">
                        <div class="p-2 bg-success bg-opacity-10 text-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Security</p>
                            <p class="fw-semibold mb-0">Encrypted credentials</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3 d-flex flex-row align-items-center gap-3 border shadow-sm h-100">
                        <div class="p-2 bg-purple bg-opacity-10 text-purple">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Redirect URI</p>
                            <p class="fw-semibold mb-0">Callback endpoint</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <x-modal.status />
</x-admin-layout>