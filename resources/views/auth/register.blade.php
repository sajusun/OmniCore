@extends('auth.app')

@section('content')
<div class="auth-card" style="max-width: 480px;">
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

        <x-form.text
            name="name"
            label="Full Name"
            value="{{ old('name') }}"
            placeholder="John Doe"
            autofocus
        />

        <x-form.email
            name="email"
            label="Email Address"
            value="{{ old('email') }}"
            placeholder="name@company.com"
        />

        <div class="row">
            <div class="col-md-6">
                <x-form.password
                    name="password"
                    label="Password"
                    placeholder="••••••••"
                />
            </div>
            <div class="col-md-6">
                <x-form.password
                    name="password_confirmation"
                    label="Confirm Password"
                    placeholder="••••••••"
                />
            </div>
        </div>

        <x-form.submit class="btn btn-primary w-100 mt-2">Create Account</x-form.submit>
    </form>

    <div class="auth-links">
        <span class="text-muted">Already have an account?</span>
        <a href="{{ route('login') }}">Sign in here</a>
    </div>
</div>
@endsection
