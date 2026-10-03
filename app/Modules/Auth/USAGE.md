# Enterprise Authentication & Security Module (`Auth`)

Comprehensive authentication system featuring Sanctum tokens, OTP verification (SMS/Email/WhatsApp), brute force lockout, password strength enforcement, and social authentication.

---

## 🎯 1. Use Cases
- Secure user registration with NIST-compliant password strength checking.
- Multi-channel OTP verification with rate limits and expiry.
- Password reset, profile management, and session invalidation.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- Laravel Sanctum, Spatie Permission.

---

## 🗄️ 3. Database Tables Created
- `users` - Core user accounts.
- `user_otps` - OTP codes with expiration and brute-force retry counters.

---

## 🛣️ 4. Key Routes
- API: `POST /api/v1/auth/register`, `POST /api/v1/auth/login`.
- API: `POST /api/v1/auth/otp/verify`, `POST /api/v1/auth/otp/resend`.
- API: `POST /api/v1/auth/password/forgot`, `POST /api/v1/auth/password/reset`.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `UserRegisteredEvent`, `PasswordResetEvent`, `OtpGeneratedEvent`.
- Listens: None.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Core module included in project foundation.
2. Run `php artisan migrate`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*