@extends('auth.app')

@section('content')
<div class="auth-card">
    <div class="auth-logo">
        <a href="{{ route('home') }}">
            <img src="{{ asset($settings->logo ?? 'default/logo.png') }}" alt="Logo">
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

        <x-form.email
            name="email"
            label="Email Address"
            value="{{ old('email') }}"
            placeholder="name@company.com"
            autofocus
        />

        <x-form.submit class="btn btn-primary w-100 mt-2">Email Password Reset Link</x-form.submit>
    </form>

    <div class="auth-links">
        <a href="{{ route('login') }}"><i class="fa fa-arrow-left me-1"></i> Back to sign in</a>
    </div>
</div>
@endsection