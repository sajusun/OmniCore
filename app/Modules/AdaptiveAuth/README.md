# Adaptive Device & Location-Based Authentication (Smart 2FA)

A standalone, plug-and-play Laravel module providing **Risk-Based / Contextual Multi-Factor Authentication**. It verifies unknown devices and suspicious IP locations via email OTP while providing seamless, frictionless single-factor access for trusted environments.

---

## 🚀 Key Features

* **Intelligent Device Fingerprinting**: Combines secure HTTP-only signed tokens, device characteristics (Platform, Browser, Form Factor), and digital entropy hashes.
* **Smart Geolocation Recognition**: Detects country/city shifts (impossible travel) without penalizing mobile users on dynamic local IPs.
* **Frictionless Experience for Trusted Devices**: Once verified, users enjoy direct login without repetitive OTP prompts for up to 60 days (configurable).
* **Automated Security Notifications**: Dispatches instant email alerts to account owners whenever a new device is successfully verified.
* **Rate-Limited OTP Engine**: Protected against brute force (default: 3 attempts, 10 min expiration, 60s resend cooldown).
* **Multi-Platform Support**: Works out of the box for standard **Laravel Web (Blade/Livewire)** and **REST APIs (JWT / Sanctum / Mobile Apps)**.
* **Zero External Paid Dependencies**: Works natively with Laravel without requiring commercial fingerprinting APIs.

---

## 📦 How to Plug & Play in ANY Laravel Project

### 1. Copy the Module
Copy the entire `AdaptiveAuth` directory into your project's `app/Modules/` (or `packages/`):
```text
app/Modules/AdaptiveAuth/
├── Config/
├── Database/Migrations/
├── Http/Controllers/
├── Http/Middleware/
├── Mail/
├── Models/
├── Providers/
├── Resources/views/
├── Routes/
├── Services/
└── Traits/
```

### 2. Register Service Provider
In Laravel 11/12, add to `bootstrap/providers.php`:
```php
return [
    // ...
    App\Modules\AdaptiveAuth\Providers\AdaptiveAuthServiceProvider::class,
];
```
*(Or in Laravel 10 and below, add it to `config/app.php` under `'providers'`)*.

### 3. Run Migrations
```bash
php artisan migrate
```
This creates:
* `user_devices` (stores trusted device tokens, browser metadata, and activity)
* `device_login_challenges` (tracks pending OTP verification sessions)
* `device_login_logs` (audit history of all login attempts and outcomes)

### 4. Add Trait to your Authenticatable Model (`User.php`)
```php
use App\Modules\AdaptiveAuth\Traits\HasAdaptiveAuth;

class User extends Authenticatable
{
    use HasAdaptiveAuth;
    // ...
}
```

---

## 🛠️ Integration with Login Flows

### Option A: Standard Web Login Controller
In your `AuthenticatedSessionController` or `LoginController`:

```php
use App\Modules\AdaptiveAuth\Services\AdaptiveAuthService;

public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate(); // Validates password
    $user = Auth::user();

    // 1. Evaluate risk & device trust
    $adaptive = app(AdaptiveAuthService::class);
    $assessment = $adaptive->evaluateEnvironment($user, $request);

    if ($assessment['status'] === 'challenge_required') {
        $challenge = $adaptive->createChallenge($user, $assessment['metadata']);
        Auth::logout();
        $request->session()->put('adaptive_challenge_token', $challenge->challenge_token);

        return redirect()->route('adaptive.challenge', ['token' => $challenge->challenge_token]);
    }

    // 2. If trusted, attach secure cookie to response
    $request->session()->regenerate();
    $response = redirect()->intended('/dashboard');

    if (isset($assessment['cookie'])) {
        $response->withCookie($assessment['cookie']);
    }

    return $response;
}
```

### Option B: REST API Login Controller (Sanctum / JWT / Mobile)
In your `LoginApiController`:

```php
use App\Modules\AdaptiveAuth\Services\AdaptiveAuthService;

public function login(Request $request): JsonResponse
{
    // Credentials validation...
    if (!Hash::check($request->password, $user->password)) {
        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    // 1. Evaluate risk & device trust
    $adaptive = app(AdaptiveAuthService::class);
    $assessment = $adaptive->evaluateEnvironment($user, $request);

    if ($assessment['status'] === 'challenge_required') {
        $challenge = $adaptive->createChallenge($user, $assessment['metadata']);

        return response()->json([
            'status'          => 'CHALLENGE_REQUIRED',
            'challenge_token' => $challenge->challenge_token,
            'message'         => 'New device detected. Please verify with the 6-digit OTP code sent to your email.',
        ], 200);
    }

    // 2. Direct login for trusted device
    $token = $user->createToken('auth')->plainTextToken; // or JWT auth('api')->login($user)
    
    $response = response()->json([
        'token' => $token,
        'user'  => $user,
    ]);

    if (isset($assessment['cookie'])) {
        $response->withCookie($assessment['cookie']);
    }

    return $response;
}
```

---

## 🌐 Routes & Endpoints

### Web Routes:
* `GET  /adaptive-auth/challenge?token={challenge_token}` &mdash; Beautiful OTP input screen.
* `POST /adaptive-auth/verify` &mdash; Submits OTP code & authenticates user.
* `POST /adaptive-auth/resend` &mdash; Resends fresh OTP code with cooldown.
* `GET  /adaptive-auth/devices` &mdash; **Security Dashboard (Blade view)** listing recognized devices, current session, and login audit logs.
* `POST /adaptive-auth/devices/{id}/revoke` &mdash; Revokes access for a specific device.
* `POST /adaptive-auth/devices/revoke-others` &mdash; Revokes access for all other devices.
* `POST /adaptive-auth/audit-logs/clear` &mdash; Clears all sign-in audit history logs.

### API Endpoints:
* `POST   /api/adaptive-auth/verify` &mdash; Accepts `{ challenge_token, otp }`, returns Bearer Token.
* `POST   /api/adaptive-auth/resend` &mdash; Accepts `{ challenge_token }`.
* `GET    /api/adaptive-auth/devices` &mdash; List active & trusted devices.
* `DELETE /api/adaptive-auth/devices/{id}` &mdash; Revoke a trusted device.
* `DELETE /api/adaptive-auth/audit-logs` &mdash; Clear all sign-in audit history logs.



---

## ⚙️ Configuration Reference (`config/adaptive_auth.php`)

```php
return [
    'enabled'             => env('ADAPTIVE_AUTH_ENABLED', true),
    'cookie_name'         => env('ADAPTIVE_AUTH_COOKIE_NAME', 'adaptive_device_token'),
    'trust_duration_days' => env('ADAPTIVE_AUTH_TRUST_DAYS', 60),

    'otp' => [
        'length'                  => 6,
        'expires_minutes'         => 10,
        'max_attempts'            => 3,
        'resend_cooldown_seconds' => 60,
    ],

    // 'none', 'country', 'city', or 'strict_ip'
    'geo_check_level'     => env('ADAPTIVE_AUTH_GEO_LEVEL', 'city'),

    // Auto-trust device on user's first login
    'trust_first_login'   => env('ADAPTIVE_AUTH_TRUST_FIRST_LOGIN', true),

    // Send email alert on new device verification
    'notify_on_new_device'=> env('ADAPTIVE_AUTH_NOTIFY_NEW_DEVICE', true),

    'redirect_route'      => 'admin.dashboard',
];
```
