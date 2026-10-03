# Multi-Channel Notification & Push Engine (`Notification`)

Decoupled, event-driven notification hub supporting In-App database alerts, Firebase Cloud Messaging (FCM) mobile push, Email, and WebSockets (Pusher/Reverb).

---

## 🎯 1. Use Cases
- Automated notifications for orders, tickets, sales, and reviews.
- Admin broadcast notifications and marketing campaigns.
- Real-time badge counter and in-app notification center.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- App\Models\User model, Firebase credentials (for FCM push).

---

## 🗄️ 3. Database Tables Created
- `notifications` - Multi-channel notification store with read receipts and metadata.

---

## 🛣️ 4. Key Routes
- API: `GET /api/v1/notifications` - Paginated user notifications.
- API: `POST /api/v1/notifications/{id}/read` - Mark notification as read.
- Admin: `POST /admin/notifications/broadcast` - Bulk notification push.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `NotificationCreated`.
- Listens: Subscribes via `ModuleNotificationSubscriber` to Ticket, Review, Vendor, Affiliate, and AppSupport events.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/Notification` to `app/Modules/`.
2. Register `NotificationServiceProvider`.
3. Run `php artisan migrate`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*