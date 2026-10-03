# SaaS Subscription & Feature Quota Module (`Subscription`)

Subscription management engine supporting recurring tiers (Free, Monthly, Yearly), feature entitlement checks, usage quota consumption, and wallet payments.

---

## 🎯 1. Use Cases
- SaaS pricing tiers with feature flags and usage limits.
- Automated renewal and expiration handling.
- Instant subscription purchase via integrated Wallet.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- App\Models\User model.

---

## 🗄️ 3. Database Tables Created
- `subscription_plans` - Pricing, billing period, quotas, feature flags.
- `user_subscriptions` - Active subscription records, start/end dates, renewal status.

---

## 🛣️ 4. Key Routes
- API: `GET /api/v1/subscriptions/plans` - Public pricing plans.
- API: `POST /api/v1/subscriptions/subscribe` - Purchase subscription plan.
- API: `POST /api/v1/subscriptions/cancel` - Cancel active subscription.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `SubscriptionCreatedEvent`, `SubscriptionCancelledEvent`.
- Listens: None.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/Subscription` to `app/Modules/`.
2. Register `SubscriptionServiceProvider`.
3. Run `php artisan migrate`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*