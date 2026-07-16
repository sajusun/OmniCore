@extends('auth.app')

@section('content')
<style>
    .auth-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        max-width: 480px;
        width: 100%;
        padding: 40px;
        border: 1px solid #e2e8f0;
        margin: 20px;
    }

    .auth-logo {
        text-align: center;
        margin-bottom: 25px;
    }

    .auth-logo img {
        height: 50px;
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
        margin-bottom: 25px;
        text-align: center;
    }

    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 8px;
    }

    .form-control {
        height: 44px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 10px 16px;
        font-size: 14px;
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
        margin-top: 20px;
        text-align: center;
        font-size: 14px;
    }

    .auth-links a {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 500;
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
            <img src="{{ asset($settings->logo ?? 'default/logo.png') }}" alt="Logo">
        </a>
    </div>

    <h1 class="auth-title">Create Account</h1>
    <p class="auth-subtitle">Join us and start your journey today.</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="John Doe" required autofocus>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="name@company.com" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Create Account</button>
    </form>

    <div class="auth-links">
        <span class="text-muted">Already have an account?</span>
        <a href="{{ route('login') }}">Sign in here</a>
    </div>
</div>
@endsection
