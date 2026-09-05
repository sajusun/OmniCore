@extends('auth.app')

@section('content')
    <div class="auth-card" style="max-width: 450px;">
        <div class="auth-logo">
            <a href="{{ route('home') }}">
                <img src="{{ asset($settings->logo ?? 'default/logo.png') }}" alt="Logo">
            </a>
        </div>

        <h1 class="auth-title">Set New Password</h1>
        <p class="auth-subtitle">Create a new password for your account.</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <input type="hidden" name="email" value="{{ $request->email ?? old('email') }}">

            {{-- <x-form.email name="email" label="Email Address" value="{{ $request->email ?? old('email') }}"
                placeholder="you@example.com" /> --}}

            <x-form.password name="password" label="New Password" placeholder="••••••••" />

            <x-form.password name="password_confirmation" label="Confirm Password" placeholder="••••••••" />

            <x-form.submit class="btn btn-primary w-100 mt-2">Reset Password</x-form.submit>
        </form>

        <div class="auth-links">
            <a href="{{ route('login') }}"><i class="fa fa-arrow-left me-1"></i> Back to sign in</a>
        </div>
    </div>
@endsection