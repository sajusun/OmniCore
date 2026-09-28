@extends('auth.app')

@section('content')
<style>
    .adaptive-card {
        max-width: 480px;
        margin: 0 auto;
        padding: 32px 28px;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
        border: 1px solid #edf2f7;
    }
    .shield-icon-wrapper {
        width: 60px;
        height: 60px;
        margin: 0 auto 18px;
        border-radius: 50%;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2563eb;
        font-size: 26px;
    }
    .device-banner {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 14px;
        margin: 18px 0;
        font-size: 13px;
        color: #475569;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }
    .device-banner .badge-risk {
        background: #fef3c7;
        color: #92400e;
        font-weight: 600;
        font-size: 10px;
        padding: 2px 7px;
        border-radius: 4px;
        text-transform: uppercase;
        display: inline-block;
        margin-bottom: 4px;
    }
    .otp-input-group {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin: 20px 0;
    }
    .otp-digit {
        width: 48px;
        height: 56px;
        font-size: 24px;
        font-weight: 700;
        text-align: center;
        border: 2px solid #cbd5e1;
        border-radius: 10px;
        background: #ffffff;
        transition: all 0.2s ease;
        font-family: monospace;
    }
    .otp-digit:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
        outline: none;
    }
    .timer-badge {
        font-size: 13px;
        color: #64748b;
        text-align: center;
        margin: 12px 0;
    }
    .timer-badge span {
        font-weight: 600;
        color: #1e293b;
    }
</style>

<div class="adaptive-card">
    <div class="text-center">
        <div class="shield-icon-wrapper">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <path d="m9 12 2 2 4-4"/>
            </svg>
        </div>
        <h2 class="h4 fw-bold mb-1">Verify Your Identity</h2>
        <p class="text-muted small mb-0">
            A sign-in attempt from an unrecognized device requires a quick 6-digit confirmation code.
        </p>
    </div>

    <!-- Device Details Context -->
    <div class="device-banner">
        <div style="font-size: 20px; line-height: 1;">💻</div>
        <div class="flex-grow-1">
            <span class="badge-risk">New Device Detected</span>
            <div class="fw-semibold text-dark">{{ $deviceMeta['device_name'] ?? 'Web Browser' }}</div>
            <div class="small text-secondary">
                IP: {{ $deviceMeta['ip'] ?? 'Unknown' }} &bull; Location: {{ trim(($deviceMeta['city'] ?? '') . ', ' . ($deviceMeta['country'] ?? 'Unknown'), ', ') }}
            </div>
        </div>
    </div>

    <p class="text-center small text-secondary mb-3">
        We sent a 6-digit code to <strong>{{ $maskedEmail }}</strong>
    </p>

    @if (session('success'))
        <div class="alert alert-success py-2 small text-center mb-3">
            {{ session('success') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning py-2 small text-center mb-3">
            {{ session('warning') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger py-2 small mb-3">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('adaptive.challenge.verify') }}" id="otpForm">
        @csrf
        <input type="hidden" name="challenge_token" value="{{ $challengeToken }}">
        <input type="hidden" name="otp" id="finalOtp">

        <!-- 6-digit interactive input pins -->
        <div class="otp-input-group" id="otpBoxContainer">
            @for ($i = 0; $i < 6; $i++)
                <input
                    type="text"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    maxlength="1"
                    class="otp-digit"
                    data-index="{{ $i }}"
                    autocomplete="one-time-code"
                    required
                />
            @endfor
        </div>

        <div class="timer-badge">
            Expires in <span id="countdownDisplay">--:--</span>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm" id="submitBtn">
            Verify & Continue
        </button>
    </form>

    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
        <form method="POST" action="{{ route('adaptive.challenge.resend') }}" class="m-0">
            @csrf
            <input type="hidden" name="challenge_token" value="{{ $challengeToken }}">
            <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none" id="resendBtn">
                Resend Code
            </button>
        </form>

        <a href="{{ route('login') }}" class="small text-muted text-decoration-none">
            &larr; Back to Login
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('.otp-digit');
    const finalOtpInput = document.getElementById('finalOtp');
    const form = document.getElementById('otpForm');

    // Auto-focus first input
    if (inputs.length > 0) {
        inputs[0].focus();
    }

    inputs.forEach((input, index) => {
        // Typing handler
        input.addEventListener('input', function (e) {
            const val = this.value.replace(/[^0-9]/g, '');
            this.value = val;

            if (val && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
            updateFinalOtp();
        });

        // Keydown (Backspace navigation)
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
                inputs[index - 1].focus();
            }
        });

        // Paste handler (user pastes entire 6-digit code)
        input.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
            if (pasteData) {
                const chars = pasteData.slice(0, 6).split('');
                chars.forEach((char, i) => {
                    if (inputs[i]) {
                        inputs[i].value = char;
                    }
                });
                const nextIndex = Math.min(chars.length, inputs.length - 1);
                inputs[nextIndex].focus();
                updateFinalOtp();

                if (chars.length === 6) {
                    form.submit();
                }
            }
        });
    });

    function updateFinalOtp() {
        let code = '';
        inputs.forEach(input => code += input.value);
        finalOtpInput.value = code;
    }

    form.addEventListener('submit', function (e) {
        updateFinalOtp();
        if (finalOtpInput.value.length < 6) {
            e.preventDefault();
            alert('Please enter all 6 digits of your verification code.');
        }
    });

    // Countdown Timer Logic
    let totalSeconds = {{ $secondsLeft ?? 600 }};
    const display = document.getElementById('countdownDisplay');

    function updateTimer() {
        if (totalSeconds <= 0) {
            display.textContent = 'Expired';
            display.style.color = '#ef4444';
            return;
        }

        const mins = Math.floor(totalSeconds / 60);
        const secs = totalSeconds % 60;
        display.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        totalSeconds--;
    }

    updateTimer();
    setInterval(updateTimer, 1000);
});
</script>
@endsection
