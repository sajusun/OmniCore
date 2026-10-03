# In-App Support & Bug Reporting Module (`AppSupport`)

Lightweight user feedback, bug report, and customer service ticket system with device telemetry capture (OS, model, app version) and automated system acknowledgment.

---

## 🎯 1. Use Cases
- Mobile app and web application bug reporting with telemetry.
- Direct customer inquiry desk with admin-to-user thread replies.
- Multi-channel notifications (Push, In-App, Email acknowledgment).

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- App\Models\User model.

---

## 🗄️ 3. Database Tables Created
- `app_supports` - Support tickets with OS, app version, status.
- `app_support_replies` - Thread messages between user, system, and admin.

---

## 🛣️ 4. Key Routes
- API: `POST /api/v1/app-support` - Submit bug or help request.
- API: `GET /api/v1/app-support` - List user reports.
- API: `POST /api/v1/app-support/{id}/reply` - User thread reply.
- Admin: `GET /admin/app-support` - Ticket management dashboard.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `AppSupportReportCreatedEvent`, `AppSupportAdminRepliedEvent`, `AppSupportUserRepliedEvent`.
- Listens: Decoupled via Notification subscriber.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/AppSupport` to `app/Modules/`.
2. Register `AppSupportServiceProvider`.
3. Run `php artisan migrate`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*