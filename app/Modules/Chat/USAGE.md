# Real-Time Chat & Messaging Module (`Chat`)

Full-featured messaging system supporting 1-on-1 direct messaging, public/private groups, channels, multimedia attachments, user blocking, and room settings.

---

## 🎯 1. Use Cases
- Customer-to-vendor marketplace messaging.
- Team chat rooms and community channels.
- User safety with block lists and message moderation.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- App\Models\User model, Laravel Reverb or Pusher (for real-time broadcasting).

---

## 🗄️ 3. Database Tables Created
- `chat_rooms` - Direct, group, and channel chat rooms.
- `chat_participants` - Members, roles (admin, member), mute states.
- `chat_messages` - Text, media attachments, read receipts.
- `chat_blocks` - User block lists.

---

## 🛣️ 4. Key Routes
- API: `GET /api/v1/chat/rooms` - User active chats.
- API: `POST /api/v1/chat/messages` - Send message.
- API: `POST /api/v1/chat/rooms` - Create group/direct room.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `MessageSentEvent`, `ParticipantJoinedEvent`.
- Listens: None.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/Chat` to `app/Modules/`.
2. Register `ChatServiceProvider`.
3. Run `php artisan migrate`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*