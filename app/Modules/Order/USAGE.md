# E-Commerce Order & Checkout State Machine (`Order`)

Mission-critical order processing module featuring atomic inventory decrement, strict state machine transitions (Pending -> Processing -> Shipped -> Delivered / Cancelled), and inventory restoration.

---

## 🎯 1. Use Cases
- Cart checkout with atomic database transactions to avoid race conditions.
- Order state management and tracking.
- Cancellation with automatic stock restoration.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- Product module, Cart module (or direct items payload).

---

## 🗄️ 3. Database Tables Created
- `orders` - Order header, totals, shipping/billing address, payment and fulfillment status.
- `order_items` - Line items, prices, quantities, product snapshots.

---

## 🛣️ 4. Key Routes
- API: `POST /api/v1/orders/checkout` - Atomic order creation.
- API: `GET /api/v1/orders` - User order history.
- API: `POST /api/v1/orders/{id}/cancel` - Cancel order and restore stock.
- Admin: `GET /admin/orders` - Order fulfillment dashboard.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `OrderCreatedEvent`, `OrderStatusUpdatedEvent`, `OrderCancelledEvent`.
- Listens: None.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/Order` to `app/Modules/`.
2. Register `OrderServiceProvider`.
3. Run `php artisan migrate`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*