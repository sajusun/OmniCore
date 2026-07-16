@extends('auth.app', ['title' => '419 Page Expired'])

@section('content')
<div class="page bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh; width: 100%;">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg border-0 rounded-4 bg-white p-5">
                    <div class="error-template">
                        <h1 class="display-1 fw-bolder text-warning mb-3" style="font-size: 6rem;">419</h1>
                        <h3 class="fw-semibold text-dark mb-2">Page Expired</h3>
                        <p class="text-muted mb-4 fs-16" style="line-height: 1.6;">
                            Your session has expired due to inactivity. Please refresh the page or log in again to continue.
                        </p>
                        <div>
                            <a class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm" href="{{ url()->previous() ?? url('/') }}">
                                <i class="fa fa-refresh me-2"></i> Refresh Page
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
