# Multi-Vendor Marketplace & Store Module (`Vendor`)

Comprehensive multi-vendor engine supporting store registration, staff roles & permissions, commission rate calculations, sales revenue splits, and payout requests to wallet.

---

## 🎯 1. Use Cases
- Multi-vendor e-commerce marketplace stores.
- Commission fee splits on completed sales.
- Vendor payout cashout directly to Wallet.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- App\Models\User model, Payment module (for wallet payout).

---

## 🗄️ 3. Database Tables Created
- `vendor_stores` - Store profile, commission rate, balance, total earnings.
- `vendor_members` - Store staff members and permission roles.
- `vendor_payouts` - Withdrawal and payout transaction requests.

---

## 🛣️ 4. Key Routes
- API: `GET /api/v1/vendors` - Directory of active vendor stores.
- API: `POST /api/v1/vendors/register` - Apply for vendor store.
- API: `GET /api/v1/vendors/my-store` - Vendor dashboard summary.
- API: `POST /api/v1/vendors/payout` - Request store balance payout.
- Admin: `GET /admin/vendors` - Global vendor store approvals.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `VendorStoreRegisteredEvent`, `VendorSaleRecordedEvent`, `VendorPayoutRequestedEvent`, `VendorStatusUpdatedEvent`.
- Listens: Decoupled via Notification module.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/Vendor` to `app/Modules/`.
2. Register `VendorServiceProvider`.
3. Run `php artisan migrate`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*