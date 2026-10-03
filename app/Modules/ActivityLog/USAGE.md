# Activity Log & Audit Trail Module (`ActivityLog`)

Universal audit trail capturing user actions, administrative interventions, security events, and model state changes across the entire platform.

---

## 🎯 1. Use Cases
- Compliance and regulatory tracking of sensitive user actions.
- Admin oversight of staff changes, logins, and permission updates.
- Debugging user workflows and identifying security incidents.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- App\Models\User model.

---

## 🗄️ 3. Database Tables Created
- `activity_logs` - Polymorphic log storage (subject_type, subject_id, causer_type, causer_id, properties, IP, user_agent).

---

## 🛣️ 4. Key Routes
- API: `GET /api/v1/activities` - User own activity timeline.
- Admin: `GET /admin/activities` - Global searchable audit trail with date/user filters.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `ActivityLoggedEvent`.
- Listens: None (Autonomous).

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/ActivityLog` to `app/Modules/`.
2. Register `App\Modules\ActivityLog\Providers\ActivityLogServiceProvider::class`.
3. Run `php artisan migrate`.
4. Use the `LogsActivity` trait on any Eloquent model you wish to track.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*