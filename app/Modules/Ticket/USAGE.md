# Customer Support Helpdesk & Ticketing Module (`Ticket`)

Full-lifecycle customer support ticketing system with categories, priority levels, attachments, internal staff notes, agent assignment, and domain event notifications.

---

## 🎯 1. Use Cases
- Helpdesk ticketing with multi-party conversations (Customer, Staff, Admin).
- Internal staff-only private notes.
- Agent ticket routing and assignment.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- App\Models\User model.

---

## 🗄️ 3. Database Tables Created
- `ticket_categories` - Support departments and categories.
- `tickets` - Ticket header with unique ticket_number, priority, status.
- `ticket_messages` - Conversation replies and private staff notes.

---

## 🛣️ 4. Key Routes
- API: `GET /api/v1/tickets` - List user tickets.
- API: `POST /api/v1/tickets` - Submit new support ticket.
- API: `POST /api/v1/tickets/{id}/reply` - Reply to ticket.
- API: `POST /api/v1/tickets/{id}/close` - Close ticket.
- Admin: `GET /admin/tickets` - Helpdesk agent dashboard.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `TicketCreatedEvent`, `TicketRepliedEvent`, `TicketAssignedEvent`, `TicketStatusUpdatedEvent`.
- Listens: Decoupled via Notification module.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/Ticket` to `app/Modules/`.
2. Register `TicketServiceProvider`.
3. Run `php artisan migrate`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*