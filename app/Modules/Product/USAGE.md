# Product Catalog & Inventory Module (`Product`)

E-commerce catalog engine supporting physical and digital products, SKU variations, inventory stock tracking, pricing, and category hierarchies.

---

## 🎯 1. Use Cases
- Product listings with filtering by category, brand, and price.
- Inventory control with atomic stock reservation.
- Multi-vendor product management.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- App\Models\User model.

---

## 🗄️ 3. Database Tables Created
- `products` - Title, slug, description, price, stock, SKU, status.
- `categories` - Multi-level category tree.

---

## 🛣️ 4. Key Routes
- API: `GET /api/v1/products` - Filterable catalog list.
- API: `GET /api/v1/products/{slug}` - Product detail.
- Admin: `GET /admin/products` - Inventory catalog manager.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `ProductCreatedEvent`, `StockLevelChangedEvent`.
- Listens: None.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/Product` to `app/Modules/`.
2. Register `ProductServiceProvider`.
3. Run `php artisan migrate`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*