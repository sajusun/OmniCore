# Social OAuth Authentication Module (`Social`)

Plug-and-play Socialite login integration for Google, Facebook, Apple, and GitHub with automated user account matching and profile syncing.

---

## 🎯 1. Use Cases
- One-click sign-in with Google, Facebook, Apple, or GitHub.
- Linking social identities to existing user accounts.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- Laravel Socialite package, App\Models\User model.

---

## 🗄️ 3. Database Tables Created
- `social_accounts` - Provider (google, facebook), provider_user_id, user_id, tokens.

---

## 🛣️ 4. Key Routes
- Web: `GET /auth/{provider}/redirect` - Redirect to OAuth provider.
- Web: `GET /auth/{provider}/callback` - Handle callback and log in user.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `SocialAccountLinkedEvent`.
- Listens: None.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/Social` to `app/Modules/`.
2. Register `SocialServiceProvider`.
3. Add provider client IDs & secrets to `config/services.php` and `.env`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*