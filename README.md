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

**One-Dashboard** is a production-ready, enterprise-grade backend platform built on **Laravel 11** utilizing a **Modular Monolith Architecture**. It is engineered for high-scale applications requiring real-time communication, social networking graphs, WebRTC audio/video conferencing, polymorphic media asset pipelines, Shopify-grade e-commerce catalog with dynamic variant matrices, and multi-guard role-based access control (RBAC).

---

## 🏛️ Architecture & Core Modules

The application is structured into decoupled domain modules under `app/Modules/`, each encapsulating its own **Controllers, Services, Models, Migrations, Enums, Events, Form Requests, API Resources, and Routes**.

```
app/Modules/
├── 🛍️ Product/          ── Shopify-Grade E-Commerce Catalog, Cartesian Variant Matrix, Categories & Inventory
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

## 🌟 Feature Highlights

### 🛍️ 1. E-Commerce Products Engine (`app/Modules/Product`)
* **Flexible Product Architectures**: Support for **Simple Products**, **Variable Products with Variant Matrices**, and **Digital Downloadables**.
* **Dynamic Cartesian Variant Matrix Generator**: Automatically computes all attribute combinations (e.g. `[Color: Red, Blue] × [Size: S, M, L]` ➔ 6 variants generated with custom SKU algorithms, individual pricing, barcodes, and stock levels).
* **Multi-Level Category Tree**: Recursive parent-child category hierarchy with automatic breadcrumb paths (`Electronics > Computers > Laptops`).
* **Deep Multi-Facet Filtering Catalog Engine**: High-performance querying supporting filters by Category, Brand, Price range (`min_price` to `max_price`), Stock status, Star rating (4+ stars), and Sorting (`newest`, `popular`, `best_selling`, `price_low_high`, `price_high_low`).
* **Inventory & Stock Management**: Threshold-based low-stock alerts, live stock adjustments (set/increment/decrement), and backorder management.
* **Customer Social Proof & Wishlist**: Verified buyer review badges, 1-5 star ratings, live average rating recalculation, photo reviews via `MediaService`, and customer wishlist toggle.
* **Polymorphic Media Handshake**: Automatic S3/Local disk resolution for thumbnails, product galleries, variant images, category icons, and review attachments.

---

### 💬 2. Real-Time Chat Engine (`app/Modules/Chat`)
* **Room Architecture**: 1-to-1 Direct Messages, Group Chats, and Broadcast Channels with Owner/Admin/Member role hierarchies.
* **Seen & Read Receipts (✓✓)**: Dynamic `last_read_message_id` tracking with real-time `MessagesRead` broadcasting and read-receipt timestamps.
* **Real-time Typing Presence (✍️)**: Zero-lag WebSocket `UserTyping` presence broadcasting.
* **Message Emoji Reactions**: Interactive reactions (❤️, 👍, 😂, 🔥, 😮, 😢) with real-time reaction summaries (`MessageReactionUpdated`).
* **Pinned Messages (📌 Telegram Style)**: Pin and unpin critical announcements in 1-on-1 and group chats.
* **Message Forwarding (↗️)**: Multi-message selection and atomic forwarding to multiple target rooms.
* **In-Chat Search & Shared Media Gallery (🖼️)**: Full-text search and filterable gallery (Photos, Videos, Audios, Documents).
* **Smart Mute Presets (🔇)**: 1 Hour, 8 Hours, 1 Day, 7 Days, Forever, or Custom Datetime.
* **Global Unread Badge Counter (🔴)**: Single-call unread count aggregator for bottom navigation badges.

---

### 📞 3. Audio/Video Call & Screen Sharing (`app/Modules/Call`)
* **State Machine Lifecycle**: `initiating` ➔ `ringing` ➔ `connected` ➔ `ended` / `missed` / `rejected` / `busy`.
* **WebRTC Signaling Engine**: Real-time SDP Offer/Answer relay and ICE Candidate exchange over WebSocket channels.
* **Screen Sharing & Track Controls**: Instant mic mute/unmute, video camera toggle, and screen share stream broadcast (`TrackStateChanged`).
* **Chat Integration (Handshake)**: Automatically logs call summaries (duration, missed, cancelled) into linked chat rooms.
* **Call Logs & History**: Complete audit history with duration tracking and missed call filters.

---

### 🌐 4. Social Graph & Relations (`app/Modules/Social`)
* **Universal Trait Integration (`HasSocialRelations`)**: Plugs directly into `User` model providing `$user->friends()`, `$user->followers()`, `$user->isFriend()`, etc.
* **Friendship Lifecycle**: Send request, Accept (with follow-sync), Reject, Cancel, Unfriend.
* **Mutual Friends Engine**: High-performance SQL join queries for mutual friend listing and count.
* **Friendship Suggestions**: Smart algorithm ranking potential connections based on mutual connections and network proximity.
* **Follower Graph**: Follow, Unfollow, Toggle follow, Followers/Followings list.
* **Block & Privacy System**: Blocking automatically terminates mutual friendships and follows both ways.
* **Profile Relationship Card (`GET /api/users/{id}/relationship`)**: Instant status card returning friendship state, follow state, block state, and mutual friend counts.

---

### 🖼️ 5. Polymorphic Media Pipeline (`app/Modules/Media`)
* **Central `MediaService`**: Single source of truth for file uploads, updates, deletions, and collection management.
* **Zero Broken Links (S3 & Local Disk-Aware)**: Relative paths stored in database; URLs resolved dynamically via `Storage::disk($disk)->url($path)`.
* **Temporary Signed URLs**: Automatic support for private cloud storage buckets (`$media->getTemporaryUrl(30)`).
* **Collision-Free Storage**: Slugified filenames with unique timestamps and random hashes.
* **Automatic Storage Cleanup**: Deleting a database record automatically purges the physical file from S3 or local disk.
* **Model Accessors (`HasMedia`)**: Instant access to `$user->avatar_url`, `$user->cover_photo_url`, `$product->thumbnail_url`, `$product->gallery_urls`.

---

### 🛡️ 6. Multi-Guard Authentication & RBAC
* **JWT Authentication** for high-performance Mobile & Web APIs.
* **Spatie Permission Engine**: Granular permissions grouped by domain (`POST_LIST`, `POST_SHOW`, `USER_MANAGE`, etc.).
* **Super Admin Bypass**: Global role bypass for administrative overrides.

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

# AWS S3 (Optional - when FILESYSTEM_DISK=s3)
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket_name
AWS_USE_PATH_STYLE_ENDPOINT=false
```

---

### 3. Database Migration & Seeding

```bash
# Create storage symlink
php artisan storage:link

# Run fresh migrations with initial seeders
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

## 📡 API Endpoint Reference

All API endpoints are prefixed with `/api` and secured with JWT `auth:api` middleware unless specified as public.

---

### 🛍️ E-Commerce Storefront Endpoints (`/api/v1/store`)

| Method | Endpoint | Auth Required | Request Parameters / Payload | Description & Working Mechanism |
|---|---|---|---|---|
| `GET` | `/api/v1/store/products` | No | **Query**: `search` (string), `category` (slug/id), `brand` (slug/id), `min_price` (num), `max_price` (num), `in_stock` (bool), `featured` (bool), `rating` (1-5), `sort` (`newest`, `popular`, `best_selling`, `price_low_high`, `price_high_low`), `per_page` (int) | **Catalog Multi-Facet Filter Engine**: Queries active products with calculated discounts, thumbnail, and eager-loaded relations. |
| `GET` | `/api/v1/store/products/featured` | No | **Query**: `limit` (default: 8) | Fetches showcase high-converting products marked with `is_featured = true`. |
| `GET` | `/api/v1/store/products/{slug}` | No | **Path**: `slug` (string) | **Product Details View**: Increments `views_count`, returns variant matrix, media gallery URLs, brand, breadcrumbs, rating breakdown, and user wishlist status. |
| `GET` | `/api/v1/store/products/{slug}/related` | No | **Path**: `slug`, **Query**: `limit` (default: 4) | Suggests related products in the same category or brand. |
| `GET` | `/api/v1/store/categories` | No | None | **Category Tree**: Returns recursive parent-child category tree with active subcategories and item counts. |
| `GET` | `/api/v1/store/categories/{slug}` | No | **Path**: `slug`, **Query**: same catalog filters | Returns category details, full breadcrumbs path array, and filterable products under this category (and subcategories). |
| `GET` | `/api/v1/store/brands` | No | None | Lists active brands with logos and product counts. |
| `GET` | `/api/v1/store/brands/{slug}` | No | **Path**: `slug`, **Query**: same catalog filters | Returns brand profile and all associated products. |
| `GET` | `/api/v1/store/products/{productId}/reviews` | No | **Path**: `productId`, **Query**: `per_page` (int) | Returns approved product reviews with user avatar, star rating, verified buyer badge, and photo attachments. |
| `POST` | `/api/v1/store/products/{productId}/reviews` | Yes | **Body (Form-Data)**: `rating` (1-5, required), `title` (string), `comment` (string, required), `photos[]` (images, max 5) | Submits customer review with photos uploaded to `MediaService`, automatically recalculating product average rating. |
| `GET` | `/api/v1/store/wishlist` | Yes | **Query**: `per_page` (int) | Paginated list of products saved in the customer's wishlist. |
| `POST` | `/api/v1/store/wishlist/toggle/{productId}` | Yes | **Path**: `productId` | **Atomic Wishlist Toggle**: Adds or removes product from customer wishlist and returns new status (`in_wishlist: true/false`). |

---

### 🛠️ E-Commerce Admin Management Endpoints (`/api/v1/admin`)

| Method | Endpoint | Auth | Request Parameters / Payload | Description & Working Mechanism |
|---|---|---|---|---|
| `GET` | `/api/v1/admin/products` | Yes | **Query**: `status` (`draft`/`published`/`archived`), `type`, `category_id`, `search`, `per_page` | Admin product management table with status filters. |
| `POST` | `/api/v1/admin/products` | Yes | **Body (Form-Data)**: `name` (required), `category_id`, `brand_id`, `price` (required), `compare_at_price`, `cost_price`, `stock_quantity`, `type` (`simple`/`variable`), `manage_stock`, `thumbnail` (file), `gallery[]` (files) | Creates new product and attaches media via polymorphic `MediaService`. |
| `GET` | `/api/v1/admin/products/{id}` | Yes | **Path**: `id` | Product details for admin edit modal. |
| `PUT` | `/api/v1/admin/products/{id}` | Yes | **Body**: Product fields + thumbnail/gallery uploads | Updates product specifications, pricing, and media assets. |
| `DELETE` | `/api/v1/admin/products/{id}` | Yes | **Path**: `id` | Soft-deletes product. |
| `POST` | `/api/v1/admin/products/bulk-status` | Yes | **Body (JSON)**: `ids` (array of product IDs), `status` (`draft`/`published`/`archived`) | Bulk updates publishing status across multiple products. |
| `POST` | `/api/v1/admin/products/bulk-prices` | Yes | **Body (JSON)**: `ids` (array of IDs), `type` (`percentage`/`fixed`), `value` (numeric) | Bulk adjusts prices by percentage or fixed amount and logs old price to `compare_at_price`. |
| `POST` | `/api/v1/admin/products/{id}/variants/generate` | Yes | **Body (JSON)**: `attribute_value_ids` (e.g. `[[1,2], [5,6]]`), `options` (`{price, stock_quantity}`) | **Cartesian Matrix Generator**: Computes all combinations, generates SKUs (`PROD-RED-XL`), and creates variants. |
| `PUT` | `/api/v1/admin/products/{id}/variants/{varId}` | Yes | **Body**: `sku`, `price`, `compare_at_price`, `stock_quantity`, `is_active`, `image` (file) | Updates individual variant pricing, stock, and variant image. |
| `DELETE` | `/api/v1/admin/products/{id}/variants/{varId}` | Yes | **Path**: `id`, `varId` | Removes individual variant. |
| `GET/POST` | `/api/v1/admin/categories` | Yes | **Body**: `name`, `parent_id`, `image` (file), `icon` (file), `order` | Category tree management and media upload. |
| `PUT/DELETE`| `/api/v1/admin/categories/{id}` | Yes | **Body**: Category fields | Updates or deletes category. |
| `GET/POST` | `/api/v1/admin/brands` | Yes | **Body**: `name`, `website`, `logo` (file), `is_active` | Brand catalog and logo management. |
| `PUT/DELETE`| `/api/v1/admin/brands/{id}` | Yes | **Body**: Brand fields | Updates or deletes brand. |
| `GET/POST` | `/api/v1/admin/attributes` | Yes | **Body**: `name`, `type` (`select`/`color`), `values` (array with `value`, `code`, `order`) | Manages global options (e.g. Color, Size, Material). |
| `POST` | `/api/v1/admin/attributes/{id}/values` | Yes | **Body**: `value`, `code`, `order` | Adds single value to attribute. |
| `GET` | `/api/v1/admin/inventory/low-stock` | Yes | **Query**: `per_page` | Lists products where `stock_quantity <= low_stock_threshold`. |
| `POST` | `/api/v1/admin/inventory/adjust` | Yes | **Body**: `product_id`, `variant_id` (optional), `quantity`, `action` (`set`/`increment`/`decrement`) | Real-time stock adjustment with updated stock levels. |
| `GET` | `/api/v1/admin/reviews` | Yes | **Query**: `status` (`pending`/`approved`/`rejected`), `product_id` | Moderation queue for user reviews. |
| `PUT` | `/api/v1/admin/reviews/{id}/status` | Yes | **Body**: `status` (`approved`/`rejected`/`pending`) | Approves or rejects review, updating product rating stats. |

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