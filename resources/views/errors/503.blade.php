@extends('auth.app', ['title' => '503 Service Unavailable'])

@section('content')
<div class="page bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh; width: 100%;">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg border-0 rounded-4 bg-white p-5">
                    <div class="error-template">
                        <h1 class="display-1 fw-bolder text-secondary mb-3" style="font-size: 6rem;">503</h1>
                        <h3 class="fw-semibold text-dark mb-2">Service Unavailable</h3>
                        <p class="text-muted mb-4 fs-16" style="line-height: 1.6;">
                            We are currently performing scheduled maintenance to improve our services. We'll be back shortly!
                        </p>
                        <div>
                            <button class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm" onclick="window.location.reload();">
                                <i class="fa fa-refresh me-2"></i> Try Again
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
