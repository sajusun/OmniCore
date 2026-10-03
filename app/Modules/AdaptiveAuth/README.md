# Adaptive Device & Multi-Factor Authentication (Smart 2FA & TOTP)

A standalone, plug-and-play Laravel module providing **Risk-Based / Contextual Authentication, TOTP Authenticator Apps (Google/Microsoft Authenticator), and Step-Up Protection for Sensitive Operations**.

Designed for high-availability distributed systems (multi-node EC2 instances behind AWS ALB with `SESSION_DRIVER=database` or `redis`).

---

## 🚀 Key Features

* **Stateless Multi-Node EC2 & ALB Ready**: Compatible with `SESSION_DRIVER=database` and shared cache.
* **TOTP Authenticator MFA (RFC 6238)**: Built using `pragmarx/google2fa-laravel` with QR codes, encrypted secrets, and 8 emergency single-use backup recovery codes.
* **Role-Based Policy Enforcement**:
  * **Admin / Support**: Mandatory TOTP MFA.
  * **Hotel Owner & Customer**: Adaptive Signed Device Token + Email OTP challenge on new/unrecognized device, with optional TOTP toggle.
* **Signed Device Token Cookies**: Uses `HttpOnly`, `Secure`, `SameSite=Lax` cookies rather than volatile IP-only tracking.
* **Sensitive Action Step-Up Middleware (`adaptive.step_up`)**: Requires recent password/MFA re-confirmation (15-minute sliding window) before changing bank details, payouts, or security settings.
* **AWS ALB & Edge Header Resolution**: Supports `CloudFront-Viewer-Country`, `CF-IPCountry`, and `X-Forwarded-For` with zero network latency.
* **Security & Device Dashboard**: View recognized devices, active sessions, remote revocation, and login audit logs.

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

### 3. Run Migrations
```bash
php artisan migrate
```
This creates:
* `user_devices` (trusted device tokens, browser metadata, and activity)
* `user_totp_credentials` (encrypted TOTP secret keys and hashed recovery codes)
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

## 🛠️ Protecting Sensitive Actions (Step-Up Auth)

Protect high-risk routes (e.g. Bank/Payout updates, Password/Email changes) using the `adaptive.step_up` middleware:

```php
Route::middleware(['auth', 'adaptive.step_up'])->group(function () {
    Route::get('/hotel-owner/payout-settings', [PayoutController::class, 'index']);
    Route::post('/hotel-owner/bank-details', [PayoutController::class, 'updateBankDetails']);
});
```

---

## ⚙️ Configuration Reference (`config/adaptive_auth.php`)

```php
return [
    'enabled' => env('ADAPTIVE_AUTH_ENABLED', true),
    'cookie_name' => env('ADAPTIVE_AUTH_COOKIE_NAME', 'adaptive_device_token'),
    'trust_duration_days' => 60,

    'role_policies' => [
        'admin'       => ['mode' => 'totp_mandatory', 'totp_required' => true],
        'support'     => ['mode' => 'totp_mandatory', 'totp_required' => true],
        'hotel_owner' => ['mode' => 'adaptive_otp', 'totp_optional' => true],
        'customer'    => ['mode' => 'adaptive_otp', 'totp_optional' => true],
    ],

    'step_up' => [
        'timeout_minutes' => 15,
        'confirm_route'   => 'password.confirm',
    ],
];
```
