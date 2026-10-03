# Audio & Video Calling Module (`Call`)

WebRTC signaling, Agora / LiveKit token generation, and call session state management for 1-on-1 and group voice/video calls.

---

## 🎯 1. Use Cases
- Direct in-app audio and video consultations.
- Customer support voice calling.
- Team conferences and group channels.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- App\Models\User model.
- WebRTC signaling server or Agora credentials in `.env`.

---

## 🗄️ 3. Database Tables Created
- `call_sessions` - Call participants, start/end timestamps, duration, and status.

---

## 🛣️ 4. Key Routes
- API: `POST /api/v1/calls/initiate` - Start call and get RTC token.
- API: `POST /api/v1/calls/{id}/end` - Terminate call session.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `CallInitiatedEvent`, `CallEndedEvent`.
- Listens: None.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/Call` to `app/Modules/`.
2. Register `CallServiceProvider`.
3. Run `php artisan migrate`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*