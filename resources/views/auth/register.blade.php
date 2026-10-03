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

        <!-- Dynamic Strong Password Meter & Security Checklist -->
        <div id="passwordSecurityBox" class="mb-3 p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0; display: none;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-semibold text-secondary">Password Strength:</span>
                <span id="strengthText" class="badge bg-secondary small">Too Short</span>
            </div>

            <!-- Segmented 4-Bar Meter -->
            <div class="d-flex gap-1 mb-3" style="height: 6px;">
                <div id="bar1" class="flex-fill rounded-pill bg-light-gray" style="transition: background-color 0.25s ease;"></div>
                <div id="bar2" class="flex-fill rounded-pill bg-light-gray" style="transition: background-color 0.25s ease;"></div>
                <div id="bar3" class="flex-fill rounded-pill bg-light-gray" style="transition: background-color 0.25s ease;"></div>
                <div id="bar4" class="flex-fill rounded-pill bg-light-gray" style="transition: background-color 0.25s ease;"></div>
            </div>

            <!-- Requirements Checklist Grid -->
            <div class="row g-2 small" id="pwChecklist">
                <div class="col-6" id="ruleLen">
                    <span class="pw-status-icon text-muted me-1">○</span>
                    <span class="pw-status-text text-muted">8+ characters</span>
                </div>
                <div class="col-6" id="ruleCase">
                    <span class="pw-status-icon text-muted me-1">○</span>
                    <span class="pw-status-text text-muted">Upper & lower case</span>
                </div>
                <div class="col-6" id="ruleNum">
                    <span class="pw-status-icon text-muted me-1">○</span>
                    <span class="pw-status-text text-muted">At least 1 number</span>
                </div>
                <div class="col-6" id="ruleSym">
                    <span class="pw-status-icon text-muted me-1">○</span>
                    <span class="pw-status-text text-muted">At least 1 symbol</span>
                </div>
                <div class="col-12" id="ruleMatch">
                    <span class="pw-status-icon text-muted me-1">○</span>
                    <span class="pw-status-text text-muted">Passwords match</span>
                </div>
            </div>
        </div>

        <x-form.submit id="submitBtn" class="btn btn-primary w-100 mt-2">Create Account</x-form.submit>
    </form>

    <div class="auth-links">
        <span class="text-muted">Already have an account?</span>
        <a href="{{ route('login') }}">Sign in here</a>
    </div>
</div>

<style>
    .bg-light-gray {
        background-color: #e2e8f0;
    }
    .pw-valid .pw-status-icon {
        color: #10b981 !important;
        font-weight: 700;
    }
    .pw-valid .pw-status-text {
        color: #047857 !important;
        font-weight: 600;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const box = document.getElementById('passwordSecurityBox');
    const strengthText = document.getElementById('strengthText');
    const bar1 = document.getElementById('bar1');
    const bar2 = document.getElementById('bar2');
    const bar3 = document.getElementById('bar3');
    const bar4 = document.getElementById('bar4');

    const ruleLen = document.getElementById('ruleLen');
    const ruleCase = document.getElementById('ruleCase');
    const ruleNum = document.getElementById('ruleNum');
    const ruleSym = document.getElementById('ruleSym');
    const ruleMatch = document.getElementById('ruleMatch');

    function updateRule(elem, isValid) {
        const icon = elem.querySelector('.pw-status-icon');
        const text = elem.querySelector('.pw-status-text');
        if (isValid) {
            elem.classList.add('pw-valid');
            icon.textContent = '✓';
        } else {
            elem.classList.remove('pw-valid');
            icon.textContent = '○';
        }
    }

    function evaluateStrength() {
        const val = passwordInput.value || '';
        const confirmVal = confirmInput.value || '';

        if (val.length > 0 || confirmVal.length > 0) {
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
            return;
        }

        const hasLen = val.length >= 8;
        const hasUpper = /[A-Z]/.test(val);
        const hasLower = /[a-z]/.test(val);
        const hasNum = /[0-9]/.test(val);
        const hasSym = /[^A-Za-z0-9]/.test(val);
        const hasMatch = val.length > 0 && val === confirmVal;

        updateRule(ruleLen, hasLen);
        updateRule(ruleCase, hasUpper && hasLower);
        updateRule(ruleNum, hasNum);
        updateRule(ruleSym, hasSym);
        updateRule(ruleMatch, hasMatch);

        // Calculate score out of 4 criteria
        let score = 0;
        if (hasLen) score++;
        if (hasUpper && hasLower) score++;
        if (hasNum) score++;
        if (hasSym) score++;

        // Reset bars
        bar1.style.backgroundColor = '#e2e8f0';
        bar2.style.backgroundColor = '#e2e8f0';
        bar3.style.backgroundColor = '#e2e8f0';
        bar4.style.backgroundColor = '#e2e8f0';
        strengthText.className = 'badge small';

        if (val.length === 0) {
            strengthText.textContent = 'Too Short';
            strengthText.classList.add('bg-secondary');
        } else if (score <= 1) {
            bar1.style.backgroundColor = '#ef4444';
            strengthText.textContent = 'Weak';
            strengthText.classList.add('bg-danger');
        } else if (score === 2) {
            bar1.style.backgroundColor = '#f97316';
            bar2.style.backgroundColor = '#f97316';
            strengthText.textContent = 'Fair';
            strengthText.style.backgroundColor = '#f97316';
        } else if (score === 3) {
            bar1.style.backgroundColor = '#f59e0b';
            bar2.style.backgroundColor = '#f59e0b';
            bar3.style.backgroundColor = '#f59e0b';
            strengthText.textContent = 'Good';
            strengthText.classList.add('bg-warning', 'text-dark');
        } else if (score === 4) {
            bar1.style.backgroundColor = '#10b981';
            bar2.style.backgroundColor = '#10b981';
            bar3.style.backgroundColor = '#10b981';
            bar4.style.backgroundColor = '#10b981';
            strengthText.textContent = 'Strong & Secure';
            strengthText.classList.add('bg-success');
        }
    }

    passwordInput.addEventListener('input', evaluateStrength);
    confirmInput.addEventListener('input', evaluateStrength);
});
</script>
@endsection
