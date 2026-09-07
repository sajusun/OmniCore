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