# Affiliate & Referral Marketing Module (`Affiliate`)

Complete affiliate management system supporting multi-tier commission structures, custom referral codes/links, click tracking, conversion attribution, and wallet cashouts.

---

## 🎯 1. Use Cases
- Influencer and partner referral marketing campaigns.
- Rewarding customers with commission for inviting colleagues/friends.
- Direct instant payout to integrated Wallet balance.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- App\Models\User model.
- Payment module (optional, for direct wallet cashout) or manual payouts.

---

## 🗄️ 3. Database Tables Created
- `affiliate_accounts` - User affiliate profiles, codes, commission rates.
- `affiliate_referrals` - Tracked visitor clicks and converted registrations.
- `affiliate_commissions` - Earned commissions tied to orders or custom conversions.

---

## 🛣️ 4. Key Routes
- API: `GET /api/v1/affiliate/dashboard` - Affiliate stats, code, balance.
- API: `POST /api/v1/affiliate/payout` - Request cashout to Wallet.
- Admin: `GET /admin/affiliates` - Global affiliate management & approvals.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `AffiliateCommissionEarnedEvent`, `AffiliatePayoutProcessedEvent`.
- Listens: Decoupled via `ModuleNotificationSubscriber` in Notification module.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/Affiliate` to `app/Modules/`.
2. Register `AffiliateServiceProvider`.
3. Run `php artisan migrate`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*