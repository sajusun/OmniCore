@extends('auth.app', ['title' => '404 Not Found'])

@section('content')
<div class="page bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh; width: 100%;">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg border-0 rounded-4 bg-white p-5">
                    <div class="error-template">
                        <h1 class="display-1 fw-bolder text-primary mb-3" style="font-size: 6rem;">404</h1>
                        <h3 class="fw-semibold text-dark mb-2">Page Not Found</h3>
                        <p class="text-muted mb-4 fs-16" style="line-height: 1.6;">
                            Oops! The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
                        </p>
                        <div>
                            <a class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm" href="{{ url('/') }}">
                                <i class="fa fa-home me-2"></i> Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection