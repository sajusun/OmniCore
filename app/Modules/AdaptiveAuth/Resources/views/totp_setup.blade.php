<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Up Authenticator App (TOTP) - {{ config('app.name', 'MyBergo') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 selection:bg-indigo-500 selection:text-white">

<div class="w-full max-w-xl bg-slate-900 border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl relative overflow-hidden backdrop-blur-xl">
    <!-- Header -->
    <div class="flex items-center space-x-3 mb-6">
        <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Two-Factor Authentication</h1>
            <p class="text-xs text-slate-400">Secure your account with Google Authenticator or 1Password</p>
        </div>
    </div>

    @if(session('recovery_codes'))
        <!-- Recovery Codes Success Modal/Card -->
        <div class="bg-emerald-950/40 border border-emerald-500/30 rounded-2xl p-6 mb-6">
            <div class="flex items-center space-x-2 text-emerald-400 font-semibold mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Two-Factor Authenticator Activated!</span>
            </div>
            <p class="text-xs text-slate-300 mb-4">
                Save these emergency recovery codes in a safe place. If you lose access to your authenticator device, you can use one of these codes to sign in. Each code can only be used once.
            </p>
            <div class="grid grid-cols-2 gap-2 bg-slate-950/80 p-4 rounded-xl border border-slate-800 font-mono text-sm text-slate-200">
                @foreach(session('recovery_codes') as $code)
                    <div class="select-all">{{ $code }}</div>
                @endforeach
            </div>
            <div class="mt-4">
                <a href="{{ route('adaptive.devices.index') }}" class="inline-flex items-center justify-center w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-medium text-sm transition">
                    I Have Saved My Recovery Codes
                </a>
            </div>
        </div>
    @elseif($hasTotp)
        <!-- Already Enabled -->
        <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-6 text-center">
            <div class="w-12 h-12 rounded-full bg-emerald-500/10 text-emerald-400 mx-auto flex items-center justify-center mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-white mb-1">MFA is Active</h3>
            <p class="text-xs text-slate-400 mb-6">Your account is currently protected with Authenticator App TOTP.</p>
            <form action="{{ route('adaptive.totp.disable') }}" method="POST">
                @csrf
                <button type="submit" onclick="return confirm('Are you sure you want to disable Two-Factor Authentication?')" class="px-5 py-2.5 rounded-xl bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/30 text-xs font-semibold transition">
                    Disable Authenticator MFA
                </button>
            </form>
        </div>
    @else
        <!-- Setup Steps -->
        <div class="space-y-6">
            <!-- Step 1: Scan QR Code -->
            <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-6 text-center">
                <p class="text-xs font-semibold text-indigo-400 uppercase tracking-wider mb-4">Step 1 &bull; Scan QR Code</p>
                <div class="bg-white p-4 rounded-2xl inline-block shadow-lg mb-4">
                    {!! $qrCodeSvg !!}
                </div>
                <div class="text-xs text-slate-400">
                    <span>Or enter manual key:</span>
                    <code class="block mt-1 font-mono text-indigo-300 bg-slate-900 border border-slate-800 py-1.5 px-3 rounded-lg select-all text-xs tracking-wider">
                        {{ $secretKey }}
                    </code>
                </div>
            </div>

            <!-- Step 2: Verification Code -->
            <form action="{{ route('adaptive.totp.enable') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="secret_key" value="{{ $secretKey }}">
                
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-2">Step 2 &bull; Enter 6-digit Code from Authenticator</label>
                    <input type="text" name="code" maxlength="6" inputmode="numeric" placeholder="123456" required
                        class="w-full text-center tracking-[0.4em] font-mono text-xl py-3.5 px-4 rounded-xl bg-slate-950 border @error('code') border-rose-500 @else border-slate-800 @enderror focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-white placeholder-slate-600 transition">
                    @error('code')
                        <p class="text-xs text-rose-400 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center space-x-3 pt-2">
                    <a href="{{ route('adaptive.devices.index') }}" class="w-1/3 py-3 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium text-xs text-center transition">
                        Cancel
                    </a>
                    <button type="submit" class="w-2/3 py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-xs text-center shadow-lg shadow-indigo-600/30 transition">
                        Activate & Get Recovery Codes
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

</body>
</html>
