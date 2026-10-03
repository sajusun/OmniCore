# Shopping Cart Module (`Cart`)

High-performance shopping cart supporting both authenticated users and guest sessions, item options/variants, and automatic subtotal calculation.

---

## 🎯 1. Use Cases
- E-commerce shopping cart management.
- Guest cart persistence with seamless merge upon user login.
- Cart item variant and option selections.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- App\Models\User (optional for guests), Product module (for item details).

---

## 🗄️ 3. Database Tables Created
- `cart_items` - User or session ID, product_id, quantity, options, price.

---

## 🛣️ 4. Key Routes
- API: `GET /api/v1/cart` - View current cart.
- API: `POST /api/v1/cart/items` - Add item to cart.
- API: `PUT /api/v1/cart/items/{id}` - Update quantity.
- API: `DELETE /api/v1/cart/items/{id}` - Remove item.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `CartItemAddedEvent`, `CartClearedEvent`.
- Listens: None.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/Cart` to `app/Modules/`.
2. Register `CartServiceProvider`.
3. Run `php artisan migrate`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*