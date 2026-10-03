# Adaptive & Risk-Based Authentication Module (`AdaptiveAuth`)

Evaluates device fingerprints, IP reputation, geolocation anomalies, and velocity checks to dynamically enforce Step-Up MFA or OTP verification.

---

## 🎯 1. Use Cases
- Flagging logins from unrecognized devices or foreign countries.
- Requiring Step-Up OTP when risky actions are performed (password change, payout request).
- Preventing credential stuffing and brute force attacks.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- App\Models\User model.

---

## 🗄️ 3. Database Tables Created
- `user_devices` - Recognized browsers, devices, and trust scores.
- `auth_security_logs` - Risk score audits per authentication attempt.

---

## 🛣️ 4. Key Routes
- API: `GET /api/v1/security/devices` - User trusted devices list.
- API: `POST /api/v1/security/devices/{id}/revoke` - Revoke device trust.
- Admin: `GET /admin/security/anomalies` - High risk login incidents.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `SuspiciousLoginDetectedEvent`, `DeviceTrustedEvent`.
- Listens: `Illuminate\Auth\Events\Login`.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/AdaptiveAuth` to `app/Modules/`.
2. Register `AdaptiveAuthServiceProvider`.
3. Run `php artisan migrate`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*