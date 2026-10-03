# Ratings & Customer Reviews Module (`Review`)

Decoupled polymorphic rating engine supporting 1-5 star reviews, media attachments, helpfulness voting, admin/vendor replies, and moderation.

---

## 🎯 1. Use Cases
- Customer product reviews and ratings.
- Vendor store evaluations.
- Helpful/unhelpful community voting.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- App\Models\User model.

---

## 🗄️ 3. Database Tables Created
- `reviews` - Rating (1-5), comment, verified purchase flag, status.
- `review_votes` - Community helpful/unhelpful upvotes.

---

## 🛣️ 4. Key Routes
- API: `GET /api/v1/reviews` - List approved reviews with score summary.
- API: `POST /api/v1/reviews` - Submit review with rating and photos.
- API: `POST /api/v1/reviews/{id}/vote` - Upvote helpfulness.
- API: `POST /api/v1/reviews/{id}/reply` - Vendor or staff reply.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `ReviewSubmittedEvent`, `ReviewRepliedEvent`.
- Listens: Decoupled via Notification module.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/Review` to `app/Modules/`.
2. Register `ReviewServiceProvider`.
3. Run `php artisan migrate`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*