@extends('auth.app')

@section('content')
<div class="auth-card" style="max-width: 450px;">
    <div class="auth-logo">
        <a href="{{ route('home') }}">
            <img src="{{ asset($settings->logo ?? 'default/logo.png') }}" alt="Logo">
        </a>
    </div>

    <h1 class="auth-title">Verify OTP</h1>
    <p class="auth-subtitle">Enter the code sent to your email.</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('verify.otp') }}">
        @csrf

        <x-form.email
            name="email"
            label="Email Address"
            value="{{ session('email') ?? old('email') }}"
            placeholder="you@example.com"
            readonly
        />

        <div class="mb-3">
            <label for="otp" class="form-label fw-medium">One-Time Password</label>
            <input
                type="text"
                name="otp"
                id="otp"
                placeholder="000000"
                value="{{ old('otp') }}"
                maxlength="6"
                required
                class="form-control text-center fw-bold ls-2 {{ $errors->has('otp') ? 'is-invalid' : '' }}"
            />
            @error('otp')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="alert alert-info py-2 small">
            📧 Please enter the 6-digit OTP sent to your email
        </div>

        <x-form.submit class="btn btn-primary w-100 mt-2">Verify OTP</x-form.submit>
    </form>

    <div class="auth-links">
        <a href="{{ route('verify.otp.resend.page') }}">Didn't receive code? Resend OTP</a>
    </div>
</div>
@endsection
