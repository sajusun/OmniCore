# Blog & Article Publishing Module (`Post`)

Complete content publication system supporting rich text articles, authors, categories, tags, SEO optimization, and scheduled publishing.

---

## 🎯 1. Use Cases
- Company blog, news releases, and announcements.
- Knowledge articles and tutorials.
- Content marketing and SEO articles.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- App\Models\User model.

---

## 🗄️ 3. Database Tables Created
- `posts` - Title, slug, content, featured image, published status, SEO tags.
- `post_categories` - Post taxonomies.

---

## 🛣️ 4. Key Routes
- API: `GET /api/v1/posts` - List published articles.
- API: `GET /api/v1/posts/{slug}` - Read article.
- Admin: `GET /admin/posts` - Blog management.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `PostPublishedEvent`.
- Listens: None.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/Post` to `app/Modules/`.
2. Register `PostServiceProvider`.
3. Run `php artisan migrate`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*