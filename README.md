# ⚡ OmniCore — Enterprise Modular Backend & E-Commerce Platform

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="320" alt="Laravel Logo">
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.4%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/Static%20Analysis-PHPStan%200%20Errors-brightgreen?style=for-the-badge&logo=php&logoColor=white" alt="PHPStan">
  <img src="https://img.shields.io/badge/Code%20Style-Laravel%20Pint-F05032?style=for-the-badge" alt="Pint">
  <img src="https://img.shields.io/badge/Architecture-Modular%20Monolith-4E73DF?style=for-the-badge" alt="Modular Monolith">
  <img src="https://img.shields.io/badge/CI%2FCD-GitHub%20Actions-2088FF?style=for-the-badge&logo=githubactions&logoColor=white" alt="CI/CD">
  <img src="https://img.shields.io/badge/API%20Docs-Scribe%20%2F%20OpenAPI%203.0-569A31?style=for-the-badge&logo=swagger&logoColor=white" alt="API Docs">
  <img src="https://img.shields.io/badge/RealTime-Laravel%20Reverb-FF6C37?style=for-the-badge&logo=socketdotio&logoColor=white" alt="RealTime Reverb">
</p>

---

## 📖 Executive Summary

**OmniCore** is a production-grade, enterprise backend and application ecosystem built on **Laravel 12** and **PHP 8.4** utilizing a **Modular Monolith Architecture**. Engineered for high-scale applications requiring concurrency-safe e-commerce, real-time messaging, WebRTC calling, universal polymorphic interactions, automated audit trails, and multi-gateway payment integrations.

Designed following strict Clean Architecture, Domain-Driven Design (DDD) principles, and SOLID design patterns.

---

## 🏛️ System Architecture Diagram

```mermaid
flowchart TB
    subgraph ClientLayer["Client & Integration Layer"]
        Web[Web Browser / Dashboard]
        Mobile[Mobile Apps / Flutter / React Native]
        ThirdParty[Webhooks & Third-Party Integrations]
    end

    subgraph GatewayLayer["API & Middleware Layer"]
        AuthGuard[Multi-Guard Auth / JWT & Sanctum]
        RateLimit[Throttle & Cooldown Protection]
        ResponseEnvelope[Standardized ApiResponse Envelope]
    end

    subgraph DomainModules["Decoupled Domain Modules (app/Modules/)"]
        direction TB
        subgraph Commerce["E-Commerce Domain"]
            ProductModule[Product Catalog & Cartesian Matrix]
            CartModule[Session/User Cart & Atomic Stock Lock]
            OrderModule[Order Lifecycle & State Machine]
            CouponModule[Discount & Promo Engine]
        end

        subgraph SocialAndRealtime["Social & Real-Time Domain"]
            ChatModule[Telegram-Grade Chat & Reverb WebSockets]
            CallModule[WebRTC Audio/Video Conferencing]
            PostModule[Feed, Reactions & Social Graph]
            InteractionModule[Universal Polymorphic Interactions]
        end

        subgraph FinancialAndCore["Financial & Core Services"]
            PaymentModule[Payment Strategy / Stripe, PayPal, bKash]
            AffiliateModule[Multi-Tier Referral & Commission Engine]
            VendorModule[Multi-Vendor Marketplace & Payouts]
            MediaModule[Polymorphic Cloud Media Pipeline]
            AIModule[AI Customer Service & Ticket Triage]
            AdaptiveAuthModule[Adaptive 2FA, TOTP MFA & Device Intelligence]
        end
    end

    subgraph InfrastructureLayer["Infrastructure & Storage Layer"]
        MySQL[(MySQL 8.0 Primary DB / InnoDB)]
        Redis[(Redis 7 / Caching & Task Queues)]
        CloudStorage[(AWS S3 / DigitalOcean Spaces / MinIO)]
        ReverbWS[Laravel Reverb WebSocket Server]
    end

    ClientLayer --> GatewayLayer
    GatewayLayer --> DomainModules
    DomainModules --> InfrastructureLayer
```

---

## 🌟 Key Enterprise Engineering Highlights

### 🛍️ 1. Shopify-Grade E-Commerce & Atomic Checkout
* **Cartesian Variant Matrix Generator**: Computes attribute combinations (e.g. `[Color] × [Size] × [Storage]` ➔ variants generated with custom SKU algorithms, individual barcodes, and stock levels).
* **Concurrency-Safe Atomic Checkout**: Uses **Database Transactions** and **Row-Level Pessimistic Locking (`lockForUpdate()`)** to prevent race-condition overselling under high concurrency.
* **Order Lifecycle State Machine**: Strict enum-backed transitions (`Pending -> Confirmed -> Processing -> Shipped -> Delivered`) with automatic stock rollback if an order is cancelled or refunded.
* **Smart Cart Engine**: Unauthenticated guest token carts seamlessly merge into user database carts upon login.

### 💳 2. Payment Gateway Strategy Pattern
* Decoupled driver architecture implementing `PaymentGatewayInterface`:
  - **Stripe** (Payment Intents & Webhooks)
  - **PayPal** (v2 Orders API)
  - **bKash** (Tokenized Checkout)
  - **SSLCommerz** (Hosted Gateway)
  - **In-App User Wallet** & **Manual Bank Transfer**

### 💬 3. Telegram-Grade Real-Time Chat & WebRTC Calling
* **Laravel Reverb & WebSockets**: Instant message delivery, typing indicators, read receipts, and online status broadcasting.
* **WebRTC Calling Engine**: Peer-to-peer signaling for audio/video calling and screen sharing with automated call duration logs.

### 🌟 4. Universal Polymorphic Interaction Engine (`app/Modules/Interaction`)
* **Reusable Headless Package**: Can be dropped into any Model (Posts, Products, Courses, Comments) via Trait `HasInteractions`.
* **Features**: Multi-Reaction Likes, Nested Comments & Replies with admin moderation, Multi-Collection Bookmarks/Wishlists, Anti-Spam Cooldown Views Analytics, and SEO/Expiring Private Share Links.
* **Reusable Blade UI Components**: Embeddable metric cards and moderation tables (`<x-interaction::stats-card />`, `<x-interaction::comments-table />`).

### 🛡️ 5. Enterprise Adaptive Authentication & Two-Factor (TOTP) Security (`app/Modules/AdaptiveAuth`)
A bank-grade security module providing **Risk-Based Adaptive Device Intelligence**, **Time-Based One-Time Password (TOTP) MFA**, and **Session/Device Management**. Engineered following NIST SP 800-63B and OWASP guidelines.

#### 🔑 Key Capabilities:
* **Polymorphic Architecture (`authenticatable_type`, `authenticatable_id`)**: Seamlessly binds to `User`, `Admin`, `Staff`, `HotelOwner`, or `Vendor` models via the `HasAdaptiveAuth` trait.
* **Risk-Based Device Intelligence**: Computes hardware & browser fingerprint hashes, detects IP geolocation changes, and tracks trusted devices. Recognized devices enjoy frictionless access; unrecognized devices or locations trigger an instant step-up challenge.
* **RFC 6238 Time-Based OTP (TOTP)**: Compatible with Google Authenticator, Microsoft Authenticator, 1Password, and Authy. Onboarding features persistent session secrets (preventing QR code desync on typos) with real-time SVG QR rendering.
* **Single-Use Emergency Recovery Codes**: Generates 8 cryptographically hashed backup codes with instant copy, `.txt` file export, and printable emergency cards.
* **Login Enforcement Policy (`Always Require TOTP on Every Login`)**:
  - **Strict Mode (Default `true`)**: Prompts for 6-digit TOTP on *every* login attempt regardless of device trust (essential for high-privilege administrators).
  - **Adaptive Mode**: Trusted devices remember the user for 60 days, prompting only on new or untrusted devices.
  - **Step-Up Verification (Sudo Mode)**: Toggling this policy strictly requires entering the current 6-digit TOTP code before changes are authorized.
* **High-Security 2FA Deactivation**: Disabling 2FA strictly requires re-authenticating with the user's **current account password**.
* **Unified Admin Panel UI**: Built directly into the dashboard theme (`layouts.admin` / Bootstrap 5 / Feather Icons) with dedicated `Recognized Devices` and `Two-Factor Authentication (MFA) Settings Hub` pages.

#### 🌐 Web Interface Routes:
| Route Name | URI | Description |
|---|---|---|
| `adaptive.devices.index` | `/adaptive-auth/devices` | Recognized devices dashboard & session audit log |
| `adaptive.devices.revoke` | `DELETE /adaptive-auth/devices/{id}/revoke` | Revoke a single active device session |
| `adaptive.devices.revoke_others` | `POST /adaptive-auth/devices/revoke-others` | Instant logout of all other recognized devices |
| `adaptive.totp.setup` | `/adaptive-auth/totp/setup` | Dedicated Two-Factor Authentication (MFA) Settings Hub |
| `adaptive.totp.enable` | `POST /adaptive-auth/totp/enable` | Confirm 6-digit code to activate TOTP |
| `adaptive.totp.disable` | `POST /adaptive-auth/totp/disable` | Disable 2FA (Requires current password verification) |
| `adaptive.totp.preference` | `POST /adaptive-auth/totp/preference` | Toggle Always Require TOTP policy (Requires TOTP code) |
| `adaptive.totp.regenerate_recovery_codes` | `POST /adaptive-auth/totp/regenerate-recovery-codes` | Invalidate and regenerate 8 fresh recovery codes |

#### 📱 Mobile App & SPA REST API Reference:
Designed for direct integration with **Flutter, React Native, iOS, Android, Next.js, and Vue**:

```text
POST /api/login
 ├── 200 OK ──────────────> { status: "SUCCESS", data: { token, user } }
 ├── 200 TOTP_REQUIRED ───> { status: "TOTP_REQUIRED", challenge_token: "...", message: "..." }
 └── 200 OTP_REQUIRED ────> { status: "CHALLENGE_REQUIRED", challenge_token: "...", message: "..." }
```

| Method | Endpoint | Auth | Purpose & Payload |
|---|---|---|---|
| `POST` | `/api/login` | Public | Standard login; returns `TOTP_REQUIRED` or `CHALLENGE_REQUIRED` if step-up is needed |
| `POST` | `/api/adaptive-auth/verify-totp` | Public | Verify 6-digit TOTP or backup recovery code: `{ challenge_token, code, remember_device }` |
| `POST` | `/api/adaptive-auth/verify` | Public | Verify email OTP for untrusted devices: `{ challenge_token, otp }` |
| `POST` | `/api/adaptive-auth/resend` | Public | Resend email OTP: `{ challenge_token }` |
| `GET` | `/api/adaptive-auth/devices` | Bearer Token | List all user registered devices, trust status, and MFA settings |
| `DELETE` | `/api/adaptive-auth/devices/{id}` | Bearer Token | Revoke specific device access |
| `DELETE` | `/api/adaptive-auth/devices/others` | Bearer Token | Revoke all other registered devices |
| `DELETE` | `/api/adaptive-auth/audit-logs` | Bearer Token | Clear user's login history logs |
| `POST` | `/api/adaptive-auth/totp/setup` | Bearer Token | Initialize TOTP setup: returns `{ secret_key, otp_auth_url }` |
| `POST` | `/api/adaptive-auth/totp/enable` | Bearer Token | Activate TOTP: `{ secret_key, code }` -> returns `{ recovery_codes }` |
| `POST` | `/api/adaptive-auth/totp/disable` | Bearer Token | Disable TOTP: `{ password: "current_password" }` |
| `POST` | `/api/adaptive-auth/totp/preference` | Bearer Token | Update strict login policy: `{ always_require_on_login: true, code: "123456" }` |
| `POST` | `/api/adaptive-auth/totp/regenerate-recovery-codes` | Bearer Token | Regenerate fresh recovery codes: returns 8 new plain recovery codes |

---

## 🛠️ Technology Stack & Standards

| Layer | Technology / Tool | Purpose |
|---|---|---|
| **Framework** | Laravel 12.x / PHP 8.4+ | Core application runtime & IOC container |
| **Static Analysis** | **PHPStan (Level 5 / 0 Errors)** | Strict type checking & zero runtime bugs |
| **Code Style** | **Laravel Pint** | PSR-12 strict formatting across all modules |
| **CI / CD** | **GitHub Actions** | Automated linting, static analysis, & PHPUnit suite |
| **API Documentation** | **Scribe & OpenAPI 3.0** | Interactive API Playground & Postman Collections |
| **Real-time Engine** | **Laravel Reverb / WebSockets** | High-concurrency event broadcasting |
| **Database & Cache** | MySQL 8.0+ / Redis 7 | Relational storage & sub-millisecond cache/queue |
| **Containerization** | Docker & Docker Compose | 1-command reproducible production environment |

---

## 📚 Interactive API Documentation

Interactive API Reference is powered by **Scribe & OpenAPI 3.0**:

- **Live Web Documentation**: Access `/docs` in your browser for the interactive Swagger/Scribe UI.
- **Postman Collection**: Generated at `storage/app/private/scribe/collection.json` (ready for import).
- **OpenAPI Specification**: Available at `storage/app/private/scribe/openapi.yaml`.

To regenerate the documentation:
```bash
php artisan scribe:generate --force
```

---

## ⚡ Quick Start & Installation

### Option A: Local Setup (PHP 8.4 + Composer)

```bash
# 1. Clone repository
git clone https://github.com/your-username/OmniCore.git
cd OmniCore

# 2. Install dependencies
composer install
npm install && npm run build

# 3. Environment configuration
cp .env.example .env
php artisan key:generate
php artisan jwt:secret

# 4. Migrate & Load Rich Demo Data
php artisan migrate --seed

# 5. Start Development Server & WebSocket Server
php artisan serve
php artisan reverb:start
```

### Option B: Docker Compose

```bash
# Start all services (PHP 8.4, Nginx, MySQL, Redis, Reverb, Mailpit)
docker compose up -d

# Run migrations & seed demo dataset
docker compose exec app php artisan migrate --seed
```

---

## 🧪 Testing & Code Quality Verification

```bash
# Run PHPUnit Automated Test Suite
php artisan test

# Run PHPStan Static Analysis (0 Errors)
vendor/bin/phpstan analyse --memory-limit=1G

# Verify Code Style (Laravel Pint)
vendor/bin/pint --test
```

---

## 📜 License

This project is open-sourced software licensed under the [MIT license](LICENSE).