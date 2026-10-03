# Discounts & Coupon Promotion Module (`Coupon`)

Flexible promotional engine supporting fixed or percentage discounts, minimum cart thresholds, per-user usage limits, and category restrictions.

---

## 🎯 1. Use Cases
- Promo codes for marketing campaigns (e.g. SUMMER20).
- First-time order coupons.
- Vendor-specific or category-specific discount vouchers.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- App\Models\User model.

---

## 🗄️ 3. Database Tables Created
- `coupons` - Code, discount type, value, expiry date, usage limit.
- `coupon_usages` - Audit trail of user redemptions.

---

## 🛣️ 4. Key Routes
- API: `POST /api/v1/coupons/apply` - Validate and calculate discount.
- Admin: `GET /admin/coupons` - Coupon management.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `CouponAppliedEvent`.
- Listens: None.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/Coupon` to `app/Modules/`.
2. Register `CouponServiceProvider`.
3. Run `php artisan migrate`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*