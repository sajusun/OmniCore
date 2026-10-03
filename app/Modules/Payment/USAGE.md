# Multi-Gateway Payment & Digital Wallet Module (`Payment`)

Dual payment architecture: Digital Wallet with P2P transfers, deposits, withdrawals, and Gateway adapters (Stripe, SSLCommerz, PayPal, Razorpay, Mock).

---

## 🎯 1. Use Cases
- Digital wallet balance for instant checkouts, earnings, and cashouts.
- P2P funds transfer between registered users.
- Credit card, Mobile Money, and bank gateway integrations.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- App\Models\User model.

---

## 🗄️ 3. Database Tables Created
- `wallets` - User balance, currency, status.
- `wallet_transactions` - Ledger of credits, debits, transfers.
- `payment_gateways` - Active payment processors and API keys.

---

## 🛣️ 4. Key Routes
- API: `GET /api/v1/wallet/balance` - User wallet summary.
- API: `POST /api/v1/wallet/transfer` - Send funds to another user.
- API: `POST /api/v1/payments/initiate` - Start gateway payment.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `WalletDepositedEvent`, `TransferCompletedEvent`, `PaymentSuccessEvent`.
- Listens: None.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/Payment` to `app/Modules/`.
2. Register `PaymentServiceProvider`.
3. Run `php artisan migrate`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*