@extends('auth.app')

@section('content')
<style>
    body.ltr.login-img::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.95) 0%, rgba(37, 99, 235, 0.95) 100%);
        z-index: -1;
    }
    
    .auth-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    
    .auth-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        max-width: 450px;
        width: 100%;
        overflow: hidden;
        animation: slideUp 0.5s ease-out;
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .auth-header {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        padding: 40px 20px;
        text-align: center;
        color: white;
    }
    
    .auth-header img {
        height: 50px;
        margin-bottom: 20px;
        filter: brightness(0) invert(1);
    }
    
    .auth-header h2 {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.5px;
    }
    
    .auth-header p {
        margin: 8px 0 0;
        font-size: 14px;
        opacity: 0.9;
    }
    
    .auth-body {
        padding: 40px;
    }
    
    .form-group-auth {
        margin-bottom: 20px;
    }
    
    .form-group-auth label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #4b5563;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .form-group-auth input {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        font-family: inherit;
    }
    
    .form-group-auth input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .auth-error {
        background-color: #fee2e2;
        color: #991b1b;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 13px;
        margin-bottom: 16px;
        border-left: 4px solid #dc2626;
    }
    
    .auth-btn {
        width: 100%;
        padding: 12px 24px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 16px;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
    }
    
    .auth-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
    }
    
    .auth-btn:active {
        transform: translateY(0);
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <a href="{{ route('home') }}">
                <img src="{{ asset($settings->logo ?? 'default/logo.png') }}" alt="Logo">
            </a>
            <h2>Set New Password</h2>
            <p>Create a new password for your account</p>
        </div>

        <div class="auth-body">
            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="form-group-auth">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" value="{{ old('email') }}" required>
                </div>
                @error('email')
                <div class="auth-error">{{ $message }}</div>
                @enderror

                <div class="form-group-auth">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
                @error('password')
                <div class="auth-error">{{ $message }}</div>
                @enderror

                <div class="form-group-auth">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
                </div>
                @error('password_confirmation')
                <div class="auth-error">{{ $message }}</div>
                @enderror

                <button type="submit" class="auth-btn">Reset Password</button>
            </form>
        </div>
    </div>
</div>
@endsection