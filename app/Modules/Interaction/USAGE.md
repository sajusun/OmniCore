# Social Interaction & Engagement Module (`Interaction`)

Universal polymorphic interaction engine supporting nested comments, replies, likes, bookmarks, shareable links, and view counters with anti-spam cooldown.

---

## 🎯 1. Use Cases
- Product reviews, questions & answers, comments.
- Post discussions and blog replies.
- User wishlists and bookmarking of any entity.
- SEO-friendly public and expiring private share links.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- App\Models\User model.

---

## 🗄️ 3. Database Tables Created
- `interaction_comments` - Nested polymorphic comments and replies.
- `interaction_likes` - Polymorphic like toggles.
- `interaction_bookmarks` - User saved items and wishlists.
- `interaction_shares` - Share links with optional password and expiry.
- `interaction_views` - View logs with IP cooldown.

---

## 🛣️ 4. Key Routes
- API: `POST /api/v1/interactions/comments` - Post comment/reply.
- API: `POST /api/v1/interactions/like` - Toggle like.
- API: `POST /api/v1/interactions/bookmark` - Save bookmark.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `CommentCreatedEvent`, `LikeToggledEvent`.
- Listens: None.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/Interaction` to `app/Modules/`.
2. Register `InteractionServiceProvider`.
3. Run `php artisan migrate`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*