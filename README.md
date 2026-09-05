# ⚡ One-Dashboard — Enterprise Modular Backend & Social Platform

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="320" alt="Laravel Logo">
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2">
  <img src="https://img.shields.io/badge/Architecture-Modular%20Monolith-4E73DF?style=for-the-badge" alt="Modular Monolith">
  <img src="https://img.shields.io/badge/RealTime-Laravel%20Reverb%20%2F%20WebSockets-FF6C37?style=for-the-badge&logo=postman&logoColor=white" alt="RealTime Reverb">
  <img src="https://img.shields.io/badge/Auth-JWT%20%26%20Sanctum-F05032?style=for-the-badge&logo=jsonwebtokens&logoColor=white" alt="JWT Auth">
  <img src="https://img.shields.io/badge/Database-MySQL%20%2F%20PostgreSQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="Database">
  <img src="https://img.shields.io/badge/Cloud%20Storage-AWS%20S3%20%2F%20MinIO-569A31?style=for-the-badge&logo=amazons3&logoColor=white" alt="AWS S3">
</p>

---

## 📖 Overview

**One-Dashboard** is a production-ready, enterprise-grade backend platform built on **Laravel 11** utilizing a **Modular Monolith Architecture**. It is engineered for high-scale applications requiring real-time communication, social networking graphs, WebRTC audio/video conferencing, polymorphic media asset pipelines, Shopify/Amazon-grade e-commerce (Product Catalog, Cartesian Variant Matrix, Guest/User Cart, Coupon Engine, Multi-Address Book, Atomic Order Checkout, Live Search & Analytics), and multi-guard role-based access control (RBAC).

---

## 🏛️ Architecture & Core Modules

The application is structured into decoupled domain modules under `app/Modules/`, each encapsulating its own **Controllers, Services, Models, Migrations, Enums, Events, Form Requests, API Resources, and Routes**.

```
app/Modules/
├── 🛍️ Product/          ── Shopify-Grade E-Commerce Catalog, Cartesian Variant Matrix, Categories & Recommendations
├── 🛒 Cart/             ── Guest Token Sessions, User Cart Merging, Stock Validation & Coupon Discounts
├── 🏷️ Coupon/           ── Percentage & Fixed Promo Discounts, Spend Limits & Usage Tracking
├── 📦 Order/            ── Multi-Address Book, Shipping Rates, Atomic Checkout, Order State Machine & Analytics
├── 💬 Chat/             ── Telegram/Messenger-Grade Real-Time Messaging Engine
├── 📞 Call/             ── WebRTC Audio/Video Calling & Screen Sharing Engine
├── 🌐 Social/           ── Facebook/LinkedIn-Grade Social Graph & Relations
├── 🖼️ Media/            ── Spatie-Grade Polymorphic Cloud & Local Asset Pipeline
├── 📝 Post/             ── Social Feed, Comments, Reactions & Nested Replies
├── 📜 ActivityLog/      ── Automated System Audit Trail & User Activity Logs
├── 🔔 BulkNotification/ ── Multi-Channel Push & In-App Notification Dispatcher
├── 📰 CMS/              ── Content Management, Static Pages & Dynamic Content
└── 🛠️ AppSupport/       ── Support Ticketing & Helpdesk System
```

---

## 🌟 E-Commerce Suite Highlights

### 🛍️ 1. Catalog & Dynamic Variant Matrix (`app/Modules/Product`)
* **Flexible Product Architectures**: Support for **Simple Products**, **Variable Products with Variant Matrices**, and **Digital Downloadables**.
* **Dynamic Cartesian Variant Matrix Generator**: Computes all attribute combinations (e.g. `[Color] × [Size] × [Storage]` ➔ variants generated with custom SKU algorithms, individual pricing, barcodes, and stock levels).
* **Multi-Level Category Tree**: Recursive parent-child category hierarchy with automatic breadcrumb paths (`Electronics > Computers > Laptops`).
* **Deep Multi-Facet Filtering Catalog Engine**: Queries active products supporting filters by Category, Brand, Price range (`min_price` to `max_price`), Stock status, Star rating (4+ stars), and Sorting (`newest`, `popular`, `best_selling`, `price_low_high`, `price_high_low`).
* **Instant Autocomplete & Bundle Recommendations**: Fast search bar autocomplete preview (`/api/v1/store/products/autocomplete?q=...`) and "Frequently Bought Together" bundle packages with savings calculation.
* **Customer Reviews & Ratings**: Verified buyer review badges, 1-5 star ratings, live average rating recalculation, photo reviews via `MediaService`.

---

### 🛒 2. Smart Cart Engine (`app/Modules/Cart`)
* **Guest Token & Session Carting**: Unauthenticated users can add items using guest tokens without immediate login.
* **Seamless Guest-to-User Cart Sync**: When a guest logs in, their session cart automatically merges into their user database cart.
* **Real-time Stock Protection**: Validates available stock for simple and variable items before adding or incrementing quantity.
* **Dynamic Cart Pricing Summary**: Computes subtotal, applied coupon discounts, estimated tax, shipping, and grand total.

---

### 🏷️ 3. Coupon & Promo Code Engine (`app/Modules/Coupon`)
* **Flexible Discount Schemes**: Percentage discounts (with optional maximum cap) and fixed amount discounts.
* **Rule Constraints**: Minimum order amount, total coupon usage limit, per-user usage limits, start/end dates, and active flags.
* **Automatic Cart Re-evaluation**: If cart items change and fall below the minimum threshold, coupons automatically detach gracefully.

---

### 📦 4. Multi-Address, Shipping & Atomic Checkout (`app/Modules/Order`)
* **Customer Multi-Address Book**: Manage multiple shipping & billing addresses with instant default toggle.
* **Configurable Shipping Methods**: Standard, Express, and Pickup options with configurable free shipping thresholds (e.g. Free delivery on orders over $100).
* **Atomic Checkout Engine**: Validates stock, calculates totals, creates order items, logs order history, and automatically locks/decrements stock.
* **Order Lifecycle State Machine**:
  - `pending` ➔ `confirmed` ➔ `processing` ➔ `shipped` ➔ `out_for_delivery` ➔ `delivered`.
  - If an order is `cancelled` or `refunded`, inventory stock is **automatically restored** to products and variants.
* **Real-time Order Tracking**: Public/Customer order tracking timeline (`/api/v1/store/orders/{orderNumber}/track`).
* **Executive Sales & Revenue Analytics**: Complete dashboard metrics covering Total Revenue, Orders count, Average Order Value (AOV), Monthly Sales Trend graphs, and Top 5 Selling Products.

---

## 🛠️ Technology Stack

| Layer | Technology |
|---|---|
| **Framework** | Laravel 11.x (PHP 8.2+) |
| **Real-time Server** | Laravel Reverb / WebSockets / Pusher Protocol |
| **Authentication** | `tymon/jwt-auth` (API) & Laravel Session (Web) |
| **Database** | MySQL 8.0+ / PostgreSQL |
| **File Storage** | AWS S3, DigitalOcean Spaces, MinIO, Local Disk |
| **Data Tables** | Yajra DataTables |
| **Task Queue** | Redis / Database Queue Worker |
| **Frontend Assets** | Vite & Vanilla CSS |

---

## 🚀 Getting Started

### Prerequisites
* **PHP** >= 8.2 (with `pdo`, `mbstring`, `openssl`, `fileinfo`, `gd`/`imagick`, `curl`)
* **Composer** >= 2.x
* **Node.js** >= 18.x & NPM
* **MySQL** >= 8.0 or PostgreSQL

---

### 1. Clone & Install Dependencies

```bash
# Clone the repository
git clone https://github.com/sajusun/One-Dashboard.git
cd One-Dashboard

# Install PHP packages
composer install

# Install NPM packages & build frontend assets
npm install
npm run build
```

---

### 2. Environment Configuration

```bash
# Copy example environment file
cp .env.example .env

# Generate application key & JWT secret
php artisan key:generate
php artisan jwt:secret
```

Configure your `.env` credentials:

```env
APP_NAME="One-Dashboard"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=one_dashboard
DB_USERNAME=root
DB_PASSWORD=

# Filesystem Driver (local, public, or s3)
FILESYSTEM_DISK=public

# Laravel Reverb (WebSockets)
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=one_dashboard
REVERB_APP_KEY=one_dashboard_key
REVERB_APP_SECRET=one_dashboard_secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http
```

---

### 3. Database Migration & Seeding

```bash
# Create storage symlink
php artisan storage:link

# Run fresh migrations with initial seeders (includes 37+ products, 144 variants, coupons, shipping methods)
php artisan migrate:fresh --seed
```

---

### 4. Running the Application

In separate terminal windows, run:

```bash
# 1. Start Laravel Backend API & Web Server
php artisan serve --port=8000

# 2. Start Real-time WebSocket Server (Reverb)
php artisan reverb:start --debug

# 3. Start Background Queue Worker
php artisan queue:work

# 4. Start Vite Asset Watcher (for Frontend Development)
npm run dev
```

---

## 🔑 Default Demo Accounts

After running `--seed`, the following accounts are available for testing:

| Email | Password | Role | Guard | Scope |
|---|---|---|---|---|
| `admin@admin.com` | `12345678` | **Super Admin** | `web` | Full System Access |
| `developer@developer.com` | `12345678` | **Developer** | `web` | Development Tools & Logs |
| `client@client.com` | `12345678` | **Client** | `web` | Client Dashboard |
| `retailer@retailer.com` | `12345678` | **Retailer** | `web` | Retailer Dashboard |
| `user@user.com` | `12345678` | **User** | `api` | Mobile / Frontend App User |

---

## 📡 Complete API Endpoint Reference

All API endpoints are prefixed with `/api` and secured with JWT `auth:api` middleware unless marked as public.

---

### 🛍️ Storefront Product & Catalog Endpoints (`/api/v1/store`)

| Method | Endpoint | Auth | Request Parameters / Payload | Description & Working Mechanism |
|---|---|---|---|---|
| `GET` | `/api/v1/store/products` | No | **Query**: `search`, `category`, `brand`, `min_price`, `max_price`, `in_stock`, `featured`, `rating`, `sort`, `per_page` | **Catalog Multi-Facet Filter Engine**: Queries active products with calculated discounts, thumbnail, and eager-loaded relations. |
| `GET` | `/api/v1/store/products/autocomplete` | No | **Query**: `q` (string, min 2 chars) | **Live Search Autocomplete**: Returns fast dropdown preview with title, price, thumbnail, category, rating. |
| `GET` | `/api/v1/store/products/featured` | No | **Query**: `limit` (default: 8) | Fetches showcase high-converting products marked with `is_featured = true`. |
| `GET` | `/api/v1/store/products/{slug}` | No | **Path**: `slug` (string) | **Product Details View**: Increments `views_count`, returns variant matrix, media gallery URLs, brand, breadcrumbs, rating breakdown, and user wishlist status. |
| `GET` | `/api/v1/store/products/{slug}/bundle-recommendations` | No | **Path**: `slug` | **"Frequently Bought Together"**: Returns main product + 2 complementary items with calculated bundle discount. |
| `GET` | `/api/v1/store/products/{slug}/related` | No | **Path**: `slug`, **Query**: `limit` (default: 4) | Suggests related products in the same category or brand. |
| `GET` | `/api/v1/store/categories` | No | None | **Category Tree**: Returns recursive parent-child category tree with active subcategories and item counts. |
| `GET` | `/api/v1/store/categories/{slug}` | No | **Path**: `slug`, **Query**: catalog filters | Returns category details, full breadcrumbs path array, and filterable products under this category (and subcategories). |
| `GET` | `/api/v1/store/brands` | No | None | Lists active brands with logos and product counts. |
| `GET` | `/api/v1/store/brands/{slug}` | No | **Path**: `slug`, **Query**: catalog filters | Returns brand profile and all associated products. |
| `GET` | `/api/v1/store/products/{id}/reviews` | No | **Path**: `id`, **Query**: `per_page` | Returns approved product reviews with user avatar, star rating, verified buyer badge, and photo attachments. |
| `POST` | `/api/v1/store/products/{id}/reviews` | Yes | **Body (Form-Data)**: `rating` (1-5), `title`, `comment`, `photos[]` (images) | Submits customer review with photos uploaded to `MediaService`, recalculating product average rating. |
| `GET` | `/api/v1/store/wishlist` | Yes | **Query**: `per_page` | Paginated list of products saved in customer wishlist. |
| `POST` | `/api/v1/store/wishlist/toggle/{id}` | Yes | **Path**: `id` | **Atomic Wishlist Toggle**: Adds or removes product from customer wishlist (`in_wishlist: true/false`). |

---

### 🛒 Storefront Cart & Coupons Endpoints (`/api/v1/store/cart`)

| Method | Endpoint | Auth | Request Parameters / Headers | Description & Working Mechanism |
|---|---|---|---|---|
| `GET` | `/api/v1/store/cart` | Optional | **Header/Query**: `X-Guest-Token` or `guest_token` | Retrieves current cart for guest or authenticated user with full pricing breakdown. |
| `POST` | `/api/v1/store/cart/items` | Optional | **Body (JSON)**: `product_id`, `variant_id` (optional), `quantity`, `guest_token` | Adds item to cart after verifying real-time stock levels. |
| `PUT` | `/api/v1/store/cart/items/{itemId}` | Optional | **Body (JSON)**: `quantity` | Updates item quantity with live stock verification. Passing 0 removes item. |
| `DELETE` | `/api/v1/store/cart/items/{itemId}` | Optional | None | Removes specific item from cart. |
| `DELETE` | `/api/v1/store/cart` | Optional | None | Clears all items in current cart. |
| `POST` | `/api/v1/store/cart/sync` | Yes | **Body (JSON)**: `guest_token` | **Guest Cart Merge**: Merges unauthenticated guest cart into user account upon login. |
| `POST` | `/api/v1/store/cart/apply-coupon` | Optional | **Body (JSON)**: `coupon_code` | Validates and attaches promo coupon code to cart, recalculating discount and grand total. |
| `POST` | `/api/v1/store/cart/remove-coupon` | Optional | None | Detaches applied coupon from cart. |

---

### 📦 Storefront Address, Shipping & Checkout Endpoints (`/api/v1/store`)

| Method | Endpoint | Auth | Request Parameters / Payload | Description & Working Mechanism |
|---|---|---|---|---|
| `GET` | `/api/v1/store/shipping-methods` | No | None | Lists active shipping delivery options and rates. |
| `GET` | `/api/v1/store/addresses` | Yes | None | Lists customer's saved shipping and billing addresses. |
| `POST` | `/api/v1/store/addresses` | Yes | **Body (JSON)**: `recipient_name`, `phone`, `street_address`, `apartment_suite`, `city`, `state`, `postal_code`, `country`, `is_default` | Adds new address to customer address book. |
| `PUT` | `/api/v1/store/addresses/{id}` | Yes | **Body (JSON)**: Address fields | Updates existing address. |
| `POST` | `/api/v1/store/addresses/{id}/default` | Yes | None | Sets selected address as default shipping address. |
| `DELETE` | `/api/v1/store/addresses/{id}` | Yes | None | Deletes address. |
| `POST` | `/api/v1/store/checkout` | Yes | **Body (JSON)**: `address_id` (or `shipping_address` object), `shipping_method_id`, `payment_method` (`cod`/`stripe`/`sslcommerz`/`bkash`), `customer_notes` | **Atomic Checkout Engine**: Validates stock, locks totals, creates order & items, decrements inventory, and clears cart. |
| `GET` | `/api/v1/store/orders` | Yes | **Query**: `per_page` | Paginated customer order history. |
| `GET` | `/api/v1/store/orders/{orderNumber}` | Yes | **Path**: `orderNumber` | Detailed order summary with items, variant snapshots, and delivery address. |
| `GET` | `/api/v1/store/orders/{orderNumber}/track` | Yes | **Path**: `orderNumber` | Live order timeline and progress tracker. |
| `POST` | `/api/v1/store/orders/{orderNumber}/cancel` | Yes | **Body (JSON)**: `reason` | Cancels order (if pending) and **restores inventory stock**. |

---

### 🛠️ Admin E-Commerce Management Endpoints (`/api/v1/admin`)

| Method | Endpoint | Auth | Request Parameters / Payload | Description & Working Mechanism |
|---|---|---|---|---|
| `GET` | `/api/v1/admin/analytics/ecommerce` | Yes | None | **Executive Sales Dashboard**: Total revenue, orders count, AOV, monthly sales trend graph, top 5 selling products. |
| `GET` | `/api/v1/admin/orders` | Yes | **Query**: `status`, `payment_status`, `search`, `per_page` | Admin order management table with status filters. |
| `GET` | `/api/v1/admin/orders/{id}` | Yes | **Path**: `id` | Full order review modal with customer details, items, timeline. |
| `PUT` | `/api/v1/admin/orders/{id}/status` | Yes | **Body**: `status` (`pending`, `confirmed`, `processing`, `shipped`, `delivered`, `cancelled`), `comment` | Updates order state machine. Restores stock if cancelled. |
| `PUT` | `/api/v1/admin/orders/{id}/payment` | Yes | **Body**: `payment_status` (`unpaid`, `paid`, `refunded`, `failed`), `transaction_id` | Updates payment status. |
| `GET/POST` | `/api/v1/admin/coupons` | Yes | **Body**: `code`, `type` (`percentage`/`fixed`), `value`, `min_order_amount`, `max_discount_amount`, `usage_limit`, `usage_limit_per_user` | Coupon management and creation. |
| `PUT/DELETE`| `/api/v1/admin/coupons/{id}` | Yes | **Body**: Coupon fields | Updates or deletes coupon. |
| `GET/POST` | `/api/v1/admin/products` | Yes | **Body (Form-Data)**: Product fields + `thumbnail`, `gallery[]` | Product listing and creation with polymorphic media. |
| `POST` | `/api/v1/admin/products/{id}/variants/generate` | Yes | **Body (JSON)**: `attribute_value_ids`, `options` | **Cartesian Matrix Generator**: Computes combinations and creates variants. |
| `GET` | `/api/v1/admin/inventory/low-stock` | Yes | **Query**: `per_page` | Low stock inventory alerts table. |
| `POST` | `/api/v1/admin/inventory/adjust` | Yes | **Body**: `product_id`, `variant_id`, `quantity`, `action` (`set`/`increment`/`decrement`) | Stock adjustment with audit updates. |
| `GET/PUT` | `/api/v1/admin/reviews` | Yes | **Body**: `status` (`approved`/`rejected`/`pending`) | Review moderation queue. |

---

### 💬 Real-Time Chat Endpoints (`/api/chat`)

| Method | Endpoint | Parameters / Payload | Description & Working Mechanism |
|---|---|---|---|
| `GET` | `/api/chat/unread-count` | None | Returns total unread messages count across all user rooms for navbar badges. |
| `GET` | `/api/chat/rooms` | `page`, `per_page` | Paginated chat rooms sorted by latest activity with participant meta. |
| `POST` | `/api/chat/rooms/single` | `recipient_id` (int, required) | Finds existing 1-on-1 direct room or creates a new room between two users. |
| `POST` | `/api/chat/rooms/group` | `title` (string), `participants` (array of user IDs), `avatar` (file) | Creates group room and assigns creator as group admin/owner. |
| `GET` | `/api/chat/rooms/{room}/messages` | `after_id` (int, optional for sync), `per_page` | Paginated messages with sender info, reactions, and attachments. |
| `POST` | `/api/chat/messages` | `room_id`, `message` (text), `attachments[]` (files) | Sends message, uploads media via `MediaService`, and broadcasts `MessageSent` WebSocket event. |
| `POST` | `/api/chat/messages/forward` | `message_ids[]` (array), `target_room_ids[]` (array) | Forwards selected messages to multiple rooms simultaneously. |
| `POST` | `/api/chat/rooms/{room}/read` | `last_read_message_id` (int) | Updates read receipts (Seen ✓✓) and broadcasts `MessagesRead` event. |
| `POST` | `/api/chat/rooms/{room}/typing` | `is_typing` (bool) | Broadcasts real-time typing presence (`UserTyping` event). |
| `POST` | `/api/chat/messages/{message}/reactions` | `reaction` (e.g. `❤️`, `👍`, `🔥`) | Adds/Toggles emoji reaction and broadcasts `MessageReactionUpdated`. |
| `POST` | `/api/chat/rooms/{room}/pin/{message}` | None | Toggles pinned status of message in room. |
| `GET` | `/api/chat/rooms/{room}/search` | `q` (string, required) | Full-text search within specific chat room. |
| `GET` | `/api/chat/rooms/{room}/media` | `type` (`image`, `video`, `audio`, `document`) | Shared gallery files filterable by media type. |
| `POST` | `/api/chat/rooms/{room}/settings/mute` | `duration` (`1_hour`, `8_hours`, `1_day`, `7_days`, `forever`, `custom`), `mute_until` | Mutes room notifications until preset duration expires. |

---

### 📞 Audio/Video Call Endpoints (`/api/calls`)

| Method | Endpoint | Parameters / Payload | Description & Working Mechanism |
|---|---|---|---|
| `POST` | `/api/calls/initiate` | `type` (`audio`, `video`, `screen_share`), `participants[]` (array of IDs), `room_id` (optional) | Creates call session, sets state to `initiating`, and broadcasts `IncomingCall` event. |
| `POST` | `/api/calls/{callSession}/ring` | None | Participant reports device is ringing (`ringing` state). |
| `POST` | `/api/calls/{callSession}/accept` | None | Accepts call (`connected` state), starts timer, and initiates WebRTC stream channel. |
| `POST` | `/api/calls/{callSession}/reject` | `reason` (optional: `busy`, `declined`) | Rejects call and notifies caller (`CallRejected` event). |
| `POST` | `/api/calls/{callSession}/end` | None | Ends call, calculates duration, logs call summary to chat room, and broadcasts `CallEnded`. |
| `POST` | `/api/calls/{callSession}/signal` | `target_user_id`, `signal_type` (`offer`/`answer`/`candidate`), `signal_data` (SDP/ICE) | Relays WebRTC signaling payloads peer-to-peer over WebSocket. |
| `POST` | `/api/calls/{callSession}/tracks` | `audio_muted` (bool), `video_muted` (bool), `screen_sharing` (bool) | Broadcasts mic mute, camera toggle, and screen share state changes. |
| `GET` | `/api/calls/history` | `per_page` | Paginated call log history with duration and participant info. |
| `GET` | `/api/calls/missed` | `per_page` | Filter for missed and unanswered calls. |

---

### 🌐 Social Graph Endpoints (`/api/friends` & `/api/users`)

| Method | Endpoint | Parameters / Payload | Description & Working Mechanism |
|---|---|---|---|
| `GET` | `/api/friends` | `search` (optional), `per_page` | Paginated list of friends with mutual friends count. |
| `GET` | `/api/friends/suggestions` | `limit` (default: 10) | Recommendations ranked by mutual friends and connection closeness. |
| `GET` | `/api/friends/{user}/mutual` | `per_page` | List of mutual friends shared between auth user and target user. |
| `POST` | `/api/friends/request/{user}` | None | Sends friend request. |
| `POST` | `/api/friends/accept/{request}` | None | Accepts friend request and automatically establishes follow relations. |
| `DELETE` | `/api/friends/unfriend/{user}` | None | Removes mutual friendship. |
| `POST` | `/api/users/{user}/toggle-follow` | None | Follows or unfollows target user. |
| `GET` | `/api/users/{user}/relationship` | None | Instant status card returning `is_friend`, `is_following`, `is_blocked`, `mutual_friends_count`. |
| `POST` | `/api/blocks/{user}` | None | Blocks user, automatically unfriending and unfollowing both ways. |

---

### 🖼️ Polymorphic Media Endpoints (`/api/media`)

| Method | Endpoint | Parameters / Payload | Description & Working Mechanism |
|---|---|---|---|
| `GET` | `/api/media` | `collection_name`, `disk`, `per_page` | Lists media items with relative and full URLs. |
| `GET` | `/api/media/{id}` | None | Detailed metadata (file size, MIME type, dimensions, storage disk). |
| `DELETE` | `/api/media/{media}` | None | Deletes media record and automatically purges the physical file from S3 or local disk. |
| `POST` | `/api/media/{media}/primary` | None | Sets selected media item as primary within its collection. |
| `POST` | `/api/media/sort-order` | `media_ids[]` (array of IDs in order) | Reorders gallery assets. |

---

### 📱 Device Session & Firebase Push Token Management (`/api/firebase/tokens`)

Multi-device login tracking, remote device session revocation, and Firebase Cloud Messaging (FCM) token synchronization.

| Method | Endpoint | Auth | Request Parameters / Payload | Description & Working Mechanism |
|---|---|---|---|---|
| `GET` | `/api/firebase/tokens` | Yes | None | **Active Device Sessions**: Lists all logged-in devices/sessions for the authenticated user, complete with `is_current_device: true/false`, device name, platform (`ios`/`android`/`web`), IP address, and last activity timestamp. |
| `POST` | `/api/firebase/tokens` | Yes | **Body (JSON)**: `token` (FCM string), `device_id` (UUID), `device_name` (optional), `platform` (optional) | Registers or updates device FCM push token, associating it with the current JWT session hash. |
| `DELETE` | `/api/firebase/tokens/{deviceId?}` | Yes | **Path/Body**: `deviceId` or `device_id` / `id` | **Revoke Specific Device**: Remotely logs out and deactivates a specific device session. |
| `DELETE` | `/api/firebase/tokens/others` | Yes | None | **Revoke All Other Devices**: Remotely logs out all devices except the current active session. |
| `DELETE` | `/api/firebase/tokens/all` | Yes | None | **Revoke All Devices**: Logs out and invalidates all device sessions for the authenticated user. |
| `POST` | `/api/firebase/tokens/touch` | Yes | **Body (JSON)**: `device_id` | **Activity Heartbeat**: Updates device `last_activity_at` timestamp and client metadata. |

---

### 🔔 In-App Notifications Endpoints (`/api/notifications`)

| Method | Endpoint | Auth | Parameters / Payload | Description & Working Mechanism |
|---|---|---|---|---|
| `GET` | `/api/notifications` | Yes | `per_page`, `page` | Paginated list of user notifications with sender info, action links, and read status. |
| `GET` | `/api/notifications/unread-count` | Yes | None | Returns unread notification count badge for UI navbar. |
| `POST` | `/api/notifications/{notification}/read` | Yes | **Path**: `notification` ID | Marks specific notification as read (`read_at` timestamp). |
| `POST` | `/api/notifications/read-all` | Yes | None | Marks all unread notifications as read. |
| `DELETE` | `/api/notifications/destroy-all` | Yes | None | Deletes all notifications for the authenticated user. |
| `DELETE` | `/api/notifications/{notification}` | Yes | **Path**: `notification` ID | Deletes a single notification. |

---

### 🌐 Public & Webhook Endpoints

| Method | Endpoint | Auth | Rate Limit | Description |
|---|---|---|---|---|
| `POST` | `/api/newsletter/subscribe` | No | 10 req/min | Newsletter subscription endpoint with email validation. |
| `POST` | `/api/contact-us` | No | 10 req/min | Public contact inquiries with customer message dispatch. |
| `POST` | `/api/app/webhooks/revenuecat` | No | None | RevenueCat in-app purchase and subscription lifecycle webhook handler. |

---

## 🧹 Maintenance & Optimization

```bash
# Clear all application caches
php artisan optimize:clear

# Cache configuration, routes, and events for production
php artisan optimize
```

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).