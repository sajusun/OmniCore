# Gamification, Loyalty Points & Badges Module (`Reward`)

Customer retention suite offering loyalty points earning, daily check-in streaks, Tier promotion (Bronze -> Silver -> Gold -> Platinum), unlockable badges, and cash redemption to Wallet.

---

## 🎯 1. Use Cases
- Points rewarded on purchases, reviews, and daily app visits.
- Automatic VIP tier promotions with perks.
- Points redemption into direct Wallet cash.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- App\Models\User model.

---

## 🗄️ 3. Database Tables Created
- `reward_tiers` - Tier definitions and threshold points.
- `reward_user_accounts` - User current points, tier, and check-in streak.
- `reward_badges` - Earnable badges and user achievements.
- `reward_transactions` - Ledger of points earned and redeemed.

---

## 🛣️ 4. Key Routes
- API: `GET /api/v1/rewards/overview` - User points and tier.
- API: `POST /api/v1/rewards/daily-checkin` - Claim daily check-in points.
- API: `POST /api/v1/rewards/redeem` - Convert points to wallet cash.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `PointsEarnedEvent`, `TierPromotedEvent`, `BadgeUnlockedEvent`.
- Listens: None.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/Reward` to `app/Modules/`.
2. Register `RewardServiceProvider`.
3. Run `php artisan migrate`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*