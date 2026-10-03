@extends(view()->exists('layouts.admin') ? 'layouts.admin' : (view()->exists('backend.app') ? 'backend.app' : 'layouts.app'))

@section('content')
<!-- CONTAINER -->
<div class="main-container container-fluid">

    <!-- PAGE-HEADER -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Two-Factor Authentication (MFA)</h1>
        </div>
        <div class="ms-auto pageheader-btn d-flex align-items-center gap-2">
            <a href="{{ route('adaptive.devices.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="fe fe-arrow-left me-1"></i>Back to Recognized Devices
            </a>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('adaptive.devices.index') }}">Security</a></li>
                <li class="breadcrumb-item active" aria-current="page">Two-Factor Authentication</li>
            </ol>
        </div>
    </div>
    <!-- PAGE-HEADER END -->

    <!-- FLASH NOTIFICATIONS -->
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <span class="alert-inner--icon me-2"><i class="fe fe-check-circle"></i></span>
        <span class="alert-inner--text">{{ session('success') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <span class="alert-inner--icon me-2"><i class="fe fe-alert-triangle"></i></span>
        <span class="alert-inner--text">{{ session('error') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <span class="alert-inner--icon me-2"><i class="fe fe-alert-triangle"></i></span>
        <span class="alert-inner--text">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('recovery_codes'))
    <!-- RECENTLY GENERATED RECOVERY CODES BANNER / MODAL TRIGGER -->
    <div class="card border-success shadow-sm mb-4">
        <div class="card-header bg-success-transparent py-3 d-flex align-items-center justify-content-between">
            <h5 class="card-title text-success mb-0 fw-semibold d-flex align-items-center gap-2">
                <i class="fe fe-check-circle fs-18"></i> Backup Recovery Codes Generated
            </h5>
            <span class="badge bg-success text-white">Save Immediately</span>
        </div>
        <div class="card-body">
            <p class="text-muted fs-13 mb-3">
                Please store these emergency backup recovery codes in a safe place (such as a password manager). 
                If you ever lose your phone or cannot access your authenticator app, each code can be used <strong>once</strong> to sign in to your account.
            </p>
            <div class="bg-light p-3 rounded-3 border mb-3">
                <div class="row row-cols-2 row-cols-sm-4 g-2 text-center font-monospace">
                    @foreach(session('recovery_codes') as $code)
                    <div class="col">
                        <div class="p-2 bg-white rounded border border-dashed fw-bold text-dark user-select-all shadow-xs">
                            {{ $code }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 border-top">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="copySetupCodes()">
                        <i class="fe fe-copy me-1"></i><span id="setupCopyBtnText">Copy All Codes</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="downloadSetupCodes()">
                        <i class="fe fe-download me-1"></i>Download as .txt
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="printSetupCodes()">
                        <i class="fe fe-printer me-1"></i>Print
                    </button>
                </div>
                <span class="text-muted fs-12">
                    <i class="fe fe-info me-1"></i>These codes will not be shown again after you leave this page.
                </span>
            </div>
        </div>
    </div>
    @endif

    @if ($hasTotp)
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- STATE A: TOTP 2FA IS CURRENTLY ACTIVE (MANAGEMENT HUB)           -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="row">
        <!-- Left Column: Status & Login Policy -->
        <div class="col-lg-7 col-xl-8">
            <!-- 1. Active Status Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3">
                        <div class="avatar avatar-xl br-7 bg-success-transparent text-success flex-shrink-0">
                            <i class="fe fe-shield fs-28"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                <h4 class="mb-0 fw-bold text-dark">Two-Factor Authenticator is Active</h4>
                                <span class="badge bg-success-transparent text-success fs-12 px-2.5 py-1">
                                    <i class="fe fe-check me-1"></i>Protected
                                </span>
                            </div>
                            <p class="text-muted fs-13 mb-3">
                                Your account is fortified with a Time-based One-Time Password (TOTP) authenticator application (such as Google Authenticator, 1Password, or Microsoft Authenticator).
                            </p>
                            <div class="d-flex flex-wrap gap-2 text-muted fs-12">
                                <span class="d-flex align-items-center gap-1">
                                    <i class="fe fe-smartphone text-primary"></i> Authenticator App Linked
                                </span>
                                <span>&bull;</span>
                                <span class="d-flex align-items-center gap-1">
                                    <i class="fe fe-key text-info"></i> {{ $recoveryCodesCount ?? 8 }} Recovery Codes Available
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Login Policy Enforcement Card (Always Require vs Adaptive) -->
            <div class="card border-primary-subtle shadow-sm mb-4">
                <div class="card-header py-3 bg-primary-transparent d-flex align-items-center justify-content-between">
                    <h5 class="card-title text-primary mb-0 fw-semibold d-flex align-items-center gap-2">
                        <i class="fe fe-lock fs-16"></i> Login Security Preference
                    </h5>
                    <span class="badge {{ ($alwaysRequireTotp ?? true) ? 'bg-primary' : 'bg-info' }} text-white fs-11">
                        {{ ($alwaysRequireTotp ?? true) ? 'Strict Security' : 'Adaptive Trust' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="flex-grow-1 me-3">
                            <h6 class="fw-semibold mb-1 text-dark">Always Require Authenticator (TOTP) on Every Login</h6>
                            <p class="text-muted fs-13 mb-2">
                                Choose whether to prompt for your 6-digit TOTP code on <em>every single login attempt</em>, or allow recognized/trusted devices to remember you.
                            </p>
                            <div class="alert alert-light border fs-12 mb-0 py-2">
                                <ul class="mb-0 ps-3">
                                    <li><strong>Enabled (Strict):</strong> Requires your 6-digit TOTP on every login attempt, even from recognized devices. <em>(Recommended for administrators)</em>.</li>
                                    <li><strong>Disabled (Adaptive):</strong> Recognized devices remember you and bypass the TOTP prompt for 60 days. Unknown devices still require challenge.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="form-check form-switch form-switch-md mb-2 d-inline-block">
                                <input class="form-check-input" type="checkbox" id="alwaysRequireTotpSwitch" 
                                    {{ ($alwaysRequireTotp ?? true) ? 'checked' : '' }} 
                                    onchange="handlePolicySwitchChange(this)"
                                    style="cursor: pointer; width: 48px; height: 24px;">
                            </div>
                            <div>
                                <span class="badge bg-light text-dark border fs-11" id="alwaysRequireStatusLabel">
                                    {{ ($alwaysRequireTotp ?? true) ? 'Always Enforce' : 'Adaptive Only' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Recovery Codes & Danger Zone -->
        <div class="col-lg-5 col-xl-4">
            <!-- 3. Emergency Backup Recovery Codes Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-semibold d-flex align-items-center gap-2">
                        <i class="fe fe-key text-info fs-16"></i> Backup Recovery Codes
                    </h5>
                    <span class="badge bg-info-transparent text-info fs-11">
                        {{ $recoveryCodesCount ?? 8 }} left
                    </span>
                </div>
                <div class="card-body">
                    <p class="text-muted fs-13 mb-3">
                        If you lose access to your phone or authenticator app, backup recovery codes are the only way to recover account access.
                    </p>
                    <div class="d-grid">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="confirmRegenerateCodes()">
                            <i class="fe fe-refresh-cw me-1"></i> Regenerate Fresh Codes
                        </button>
                    </div>
                    <small class="text-muted d-block mt-2 fs-11 text-center">
                        Regenerating invalidates any existing unused backup codes.
                    </small>
                </div>
            </div>

            <!-- 4. Danger Zone: Disable 2FA -->
            <div class="card border-danger shadow-sm mb-4">
                <div class="card-header bg-danger-transparent py-3">
                    <h5 class="card-title text-danger mb-0 fw-semibold d-flex align-items-center gap-2">
                        <i class="fe fe-alert-triangle fs-16"></i> Danger Zone
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-semibold text-dark mb-1">Disable Two-Factor Authentication</h6>
                    <p class="text-muted fs-12 mb-3">
                        Disabling 2FA lowers your account protection. Unrecognized devices will fall back to email verification only.
                    </p>
                    <div class="d-grid">
                        <button type="button" class="btn btn-danger btn-sm" onclick="openDisableModal()">
                            <i class="fe fe-shield-off me-1"></i> Disable 2FA
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @else
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- STATE B: TOTP 2FA IS NOT ENABLED (SETUP WIZARD)                   -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 bg-primary-transparent">
                    <h5 class="card-title text-primary mb-0 fw-semibold d-flex align-items-center gap-2">
                        <i class="fe fe-shield fs-18"></i> Setup Two-Factor Authenticator (TOTP)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted fs-13 mb-4">
                        Protect your account with an Authenticator App. Each time you sign in, in addition to your password, you will be prompted to provide a 6-digit code generated by the app.
                    </p>

                    <div class="row g-4">
                        <!-- Step 1: Scan QR Code -->
                        <div class="col-md-6 border-end-md">
                            <div class="text-center p-2">
                                <span class="badge bg-primary text-white mb-3 px-3 py-1 fs-12">Step 1 &bull; Scan QR Code</span>
                                
                                <div class="bg-white p-3 rounded-3 border d-inline-block shadow-sm mb-3">
                                    {!! $qrCodeSvg !!}
                                </div>

                                <div class="text-muted fs-12 mb-2">
                                    Can't scan the QR code? Enter this key manually:
                                </div>
                                <div class="p-2 bg-light rounded border text-monospace text-dark fs-12 fw-bold user-select-all mb-3 text-center">
                                    {{ $secretKey }}
                                </div>

                                <div>
                                    <a href="{{ route('adaptive.totp.setup', ['refresh' => 1]) }}" class="text-primary fs-12 text-decoration-none" onclick="return confirm('Regenerate a new QR code? You will need to scan the new code with your authenticator app.')">
                                        <i class="fe fe-refresh-cw me-1"></i>Regenerate a new QR code
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Verification Code -->
                        <div class="col-md-6 d-flex flex-column justify-content-center">
                            <div class="p-2">
                                <span class="badge bg-primary text-white mb-3 px-3 py-1 fs-12">Step 2 &bull; Enter 6-digit Code</span>

                                <form action="{{ route('adaptive.totp.enable') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="secret_key" value="{{ $secretKey }}">

                                    <div class="mb-3">
                                        <label for="code" class="form-label fw-semibold text-dark fs-13">
                                            Enter Code from Authenticator App <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="code" id="code" maxlength="6" inputmode="numeric" placeholder="123456" required autofocus
                                            class="form-control text-center font-monospace fs-20 fw-bold letter-spacing-lg py-2 @error('code') is-invalid @enderror"
                                            style="letter-spacing: 0.3em;">
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text text-muted fs-12 mt-2">
                                            Open Google Authenticator, 1Password, or Microsoft Authenticator and enter the current 6-digit code.
                                        </div>
                                    </div>

                                    <div class="d-grid mt-4">
                                        <button type="submit" class="btn btn-primary btn-md fw-semibold shadow-primary">
                                            <i class="fe fe-check-circle me-1"></i> Activate & Get Recovery Codes
                                        </button>
                                    </div>
                                </form>

                                <div class="mt-4 pt-3 border-top">
                                    <h6 class="fs-12 fw-semibold text-muted mb-2">Supported Authenticator Apps:</h6>
                                    <div class="d-flex flex-wrap gap-2 fs-12 text-muted">
                                        <span class="badge bg-light text-dark border">Google Authenticator</span>
                                        <span class="badge bg-light text-dark border">Microsoft Authenticator</span>
                                        <span class="badge bg-light text-dark border">1Password</span>
                                        <span class="badge bg-light text-dark border">Authy</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- MODALS & STEP-UP SECURITY CONFIRMATIONS                           -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->

    {{-- 1. Step-Up Verification Modal for Toggling Login Policy --}}
    @if ($hasTotp)
    <div class="modal fade" id="stepUpTotpPolicyModal" tabindex="-1" aria-labelledby="stepUpTotpPolicyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-primary">
                <div class="modal-header bg-primary-transparent py-3">
                    <h5 class="modal-title text-primary fw-semibold d-flex align-items-center gap-2" id="stepUpTotpPolicyModalLabel">
                        <i class="fe fe-lock fs-18"></i> Authorize Security Setting Change
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="cancelPolicyChange()"></button>
                </div>
                <form id="stepUpPolicyForm" onsubmit="submitPolicyChange(event)">
                    @csrf
                    <div class="modal-body py-4">
                        <p class="text-muted fs-13 mb-3" id="policyChangeDescription">
                            To modify your login enforcement policy, please enter the current 6-digit code from your Authenticator app.
                        </p>

                        <div class="mb-3">
                            <label for="stepup_totp_code" class="form-label fw-semibold text-dark fs-13">
                                6-Digit Authenticator (TOTP) Code <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="code" id="stepup_totp_code" maxlength="6" inputmode="numeric" placeholder="123456" required
                                class="form-control text-center font-monospace fs-20 fw-bold py-2" style="letter-spacing: 0.3em;">
                            <div id="stepup_error_feedback" class="text-danger fs-12 mt-1 d-none"></div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="cancelPolicyChange()">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="stepupSubmitBtn">
                            <i class="fe fe-check me-1"></i> Verify & Apply Change
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 2. Password Confirmation Modal for Disabling 2FA --}}
    <div class="modal fade" id="disableTotpModal" tabindex="-1" aria-labelledby="disableTotpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-danger">
                <div class="modal-header bg-danger-transparent py-3">
                    <h5 class="modal-title text-danger fw-semibold d-flex align-items-center gap-2" id="disableTotpModalLabel">
                        <i class="fe fe-shield-off fs-18"></i> Disable Two-Factor Authentication
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('adaptive.totp.disable') }}" method="POST">
                    @csrf
                    <div class="modal-body py-4">
                        <div class="alert alert-warning mb-3">
                            <i class="fe fe-alert-triangle me-1"></i>
                            <strong>Warning:</strong> Disabling 2FA will significantly lower your account security. Unknown or untrusted devices will only be protected by basic email verification.
                        </div>

                        <div class="mb-3">
                            <label for="current_password_totp" class="form-label fw-semibold text-dark">
                                Enter Current Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="password" id="current_password_totp" class="form-control @error('password') is-invalid @enderror" required placeholder="Enter your account password to confirm" autocomplete="current-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted fs-12">
                                For security reasons, please re-enter your current password to authorize disabling 2FA.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fe fe-shield-off me-1"></i> Confirm & Disable 2FA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Hidden form for regenerating recovery codes --}}
    <form id="regenerateRecoveryCodesForm" method="POST" action="{{ route('adaptive.totp.regenerate_recovery_codes') }}" style="display: none;">
        @csrf
    </form>
    @endif

</div>
<!-- CONTAINER END -->
@endsection

@push('scripts')
<script>
    const setupRecoveryCodes = @json(session('recovery_codes') ?? []);

    function copySetupCodes() {
        if (!setupRecoveryCodes || setupRecoveryCodes.length === 0) return;
        const text = "=== OMNICORE 2FA RECOVERY CODES ===\n" +
                     "Keep these emergency backup recovery codes safe.\n" +
                     "Account: {{ auth()->user()?->email }}\n" +
                     "Generated: " + new Date().toLocaleString() + "\n\n" +
                     setupRecoveryCodes.join("\n") + "\n\n" +
                     "Note: Each code can only be used once.";
        navigator.clipboard.writeText(text).then(() => {
            const btn = document.getElementById('setupCopyBtnText');
            if (btn) {
                btn.textContent = 'Copied!';
                setTimeout(() => btn.textContent = 'Copy All Codes', 2000);
            }
        });
    }

    function downloadSetupCodes() {
        if (!setupRecoveryCodes || setupRecoveryCodes.length === 0) return;
        const text = "=== OMNICORE 2FA RECOVERY CODES ===\n" +
                     "Keep these emergency backup recovery codes safe.\n" +
                     "Account: {{ auth()->user()?->email }}\n" +
                     "Generated: " + new Date().toLocaleString() + "\n\n" +
                     setupRecoveryCodes.join("\n") + "\n\n" +
                     "Note: Each code can only be used once.";
        const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'omnicore-2fa-recovery-codes.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    function printSetupCodes() {
        window.print();
    }

    function openDisableModal() {
        const modalEl = document.getElementById('disableTotpModal');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }

    @if ($errors->has('password'))
    document.addEventListener('DOMContentLoaded', function () {
        openDisableModal();
    });
    @endif

    function confirmRegenerateCodes() {
        const confirmAction = () => {
            document.getElementById('regenerateRecoveryCodesForm').submit();
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Regenerate Recovery Codes?',
                text: 'Any existing unused backup recovery codes will be permanently invalidated and replaced with fresh codes.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, generate new codes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    confirmAction();
                }
            });
        } else {
            if (confirm('Regenerate Recovery Codes? Any existing unused codes will be permanently invalidated.')) {
                confirmAction();
            }
        }
    }

    // Step-Up Policy Switch Handler
    let pendingSwitchState = null;
    let stepUpModalInstance = null;

    function handlePolicySwitchChange(switchEl) {
        pendingSwitchState = switchEl.checked;
        
        // Revert switch visually until step-up code confirms
        switchEl.checked = !pendingSwitchState;

        const modalEl = document.getElementById('stepUpTotpPolicyModal');
        if (modalEl) {
            const codeInput = document.getElementById('stepup_totp_code');
            if (codeInput) {
                codeInput.value = '';
            }
            const errorFeedback = document.getElementById('stepup_error_feedback');
            if (errorFeedback) {
                errorFeedback.classList.add('d-none');
                errorFeedback.textContent = '';
            }
            stepUpModalInstance = new bootstrap.Modal(modalEl);
            stepUpModalInstance.show();
            setTimeout(() => codeInput?.focus(), 400);
        }
    }

    function cancelPolicyChange() {
        pendingSwitchState = null;
    }

    function submitPolicyChange(e) {
        e.preventDefault();
        const codeInput = document.getElementById('stepup_totp_code');
        const errorFeedback = document.getElementById('stepup_error_feedback');
        const submitBtn = document.getElementById('stepupSubmitBtn');
        const code = codeInput ? codeInput.value.trim() : '';

        if (!code || code.length !== 6) {
            if (errorFeedback) {
                errorFeedback.textContent = 'Please enter a valid 6-digit TOTP code.';
                errorFeedback.classList.remove('d-none');
            }
            return;
        }

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Verifying...';
        }

        fetch("{{ route('adaptive.totp.preference') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                always_require_on_login: pendingSwitchState ? 1 : 0,
                code: code
            })
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(({ status, body }) => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fe fe-check me-1"></i> Verify & Apply Change';
            }

            if (status === 200 && body.status) {
                // Success: apply switch state
                const switchEl = document.getElementById('alwaysRequireTotpSwitch');
                const label = document.getElementById('alwaysRequireStatusLabel');
                if (switchEl) switchEl.checked = pendingSwitchState;
                if (label) label.textContent = pendingSwitchState ? 'Always Enforce' : 'Adaptive Only';

                if (stepUpModalInstance) {
                    stepUpModalInstance.hide();
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Security Preference Updated',
                        text: body.message,
                        timer: 2500,
                        showConfirmButton: false,
                    });
                }
            } else {
                if (errorFeedback) {
                    errorFeedback.textContent = body.message || 'Invalid Authenticator code. Please try again.';
                    errorFeedback.classList.remove('d-none');
                }
            }
        })
        .catch(err => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fe fe-check me-1"></i> Verify & Apply Change';
            }
            if (errorFeedback) {
                errorFeedback.textContent = 'Network or server error occurred. Please try again.';
                errorFeedback.classList.remove('d-none');
            }
        });
    }
</script>
@endpush
