@extends('auth.app')

@section('content')
<div class="auth-card">
    <div class="auth-logo">
        <a href="{{ route('home') }}">
            <img src="{{ asset($settings->logo ?? 'default/logo.png') }}" alt="Logo">
        </a>
    </div>

    <h1 class="auth-title">Welcome Back</h1>
    <p class="auth-subtitle">Please enter your details to sign in.</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <x-form.email
            name="email"
            label="Email Address"
            value="{{ old('email') }}"
            placeholder="name@company.com"
            autofocus
        />

        <x-form.password
            name="password"
            label="Password"
            placeholder="••••••••"
            labelActions='<a href="{{ route("password.request") }}" class="small text-primary text-decoration-none">Forgot password?</a>'
        />

        <x-form.checkbox
            name="remember"
            label="Remember me for 30 days"
        />

        <x-form.submit class="btn btn-primary w-100 mt-2">Sign in</x-form.submit>
    </form>

    <div class="auth-links">
        <span class="text-muted">Don't have an account?</span>
        <a href="{{ route('register') }}">Create an account</a>
    </div>
</div>
@endsection
