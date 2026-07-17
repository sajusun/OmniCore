@extends('auth.app')

@section('content')
<div class="auth-card">
    <div class="auth-logo">
        <a href="{{ route('home') }}">
            <img src="{{ asset($settings->logo ?? 'default/logo.png') }}" alt="Logo" class="header-brand-img">
        </a>
    </div>

    <h1 class="auth-title">Resend OTP</h1>
    <p class="auth-subtitle">Enter your email address to receive a new OTP code.</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('verify.otp.resend') }}">
        @csrf

        <x-form.email
            name="email"
            label="Email Address"
            value="{{ session('email') ?? '' }}"
            placeholder="{{ session('email') ?? 'Email' }}"
        />

        <x-form.submit class="btn btn-primary w-100 mt-2">Resend OTP</x-form.submit>
    </form>

    <div class="auth-links">
        <a href="{{ route('login') }}"><i class="fa fa-arrow-left me-1"></i> Back to sign in</a>
    </div>
</div>
@endsection
