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
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.95) 0%, rgba(109, 40, 217, 0.95) 100%);
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
        background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
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
        text-align: center;
        letter-spacing: 2px;
    }
    
    .form-group-auth input:focus {
        outline: none;
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
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
    
    .auth-info {
        background-color: #ede9fe;
        color: #5b21b6;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 13px;
        margin-bottom: 16px;
        border-left: 4px solid #8b5cf6;
    }
    
    .auth-forgot {
        text-align: center;
        margin-top: 12px;
    }
    
    .auth-forgot a {
        font-size: 13px;
        font-weight: 600;
        color: #8b5cf6;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .auth-forgot a:hover {
        color: #6d28d9;
    }
    
    .auth-btn {
        width: 100%;
        padding: 12px 24px;
        background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 16px;
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
    }
    
    .auth-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(139, 92, 246, 0.6);
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
            <h2>Verify OTP</h2>
            <p>Enter the code sent to your email</p>
        </div>

        <div class="auth-body">
            <form method="POST" action="{{ route('verify.otp') }}">
                @csrf

                <div class="form-group-auth">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" value="{{ session('email') ?? old('email') }}" readonly>
                </div>
                @error('email')
                <div class="auth-error">{{ $message }}</div>
                @enderror

                <div class="form-group-auth">
                    <label for="otp">One-Time Password</label>
                    <input type="text" id="otp" name="otp" placeholder="000000" value="{{ old('otp') }}" maxlength="6" required>
                </div>
                <div class="auth-info">📧 Please enter the 6-digit OTP sent to your email</div>
                @error('otp')
                <div class="auth-error">{{ $message }}</div>
                @enderror

                <button type="submit" class="auth-btn">Verify OTP</button>
            </form>

            <div class="auth-forgot">
                <a href="{{ route('verify.otp.resend.page') }}">Didn't receive code? Resend OTP</a>
            </div>
        </div>
    </div>
</div>
@endsection

