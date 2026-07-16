@extends('auth.app')

@section('content')
<style>
    .auth-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        max-width: 420px;
        width: 100%;
        padding: 40px;
        border: 1px solid #e2e8f0;
    }

    .auth-logo {
        text-align: center;
        margin-bottom: 30px;
    }

    .auth-logo img {
        height: 60px;
        width: auto;
    }

    .auth-title {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
        text-align: center;
    }

    .auth-subtitle {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 30px;
        text-align: center;
    }

    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 8px;
    }

    .form-control {
        height: 48px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 10px 16px;
        font-size: 15px;
        transition: all 0.2s;
    }

    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .btn-primary {
        height: 48px;
        background-color: #0f172a;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 15px;
        width: 100%;
        margin-top: 20px;
        transition: all 0.2s;
    }

    .btn-primary:hover {
        background-color: #1e293b;
        transform: translateY(-1px);
    }

    .auth-links {
        margin-top: 25px;
        text-align: center;
        font-size: 14px;
    }

    .auth-links a {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 500;
    }

    .auth-links a:hover {
        text-decoration: underline;
    }

    .alert {
        font-size: 13px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
</style>

<div class="auth-card">
    <div class="auth-logo">
        <a href="{{ route('home') }}">
            <img src="{{ asset($settings->logo ?? 'default/logo.svg') }}" alt="Logo">
        </a>
    </div>

    <h1 class="auth-title">Forgot Password?</h1>
    <p class="auth-subtitle">No problem. Just let us know your email address and we will email you a password reset link.</p>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="name@company.com" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary">Email Password Reset Link</button>
    </form>

    <div class="auth-links">
        <a href="{{ route('login') }}"><i class="fe fe-arrow-left me-1"></i> Back to sign in</a>
    </div>
</div>
@endsection