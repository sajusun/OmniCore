# Content Management System (CMS) Module (`CMS`)

Dynamic pages, landing sections, banners, SEO meta tags, FAQs, and custom content blocks editable from the admin dashboard.

---

## 🎯 1. Use Cases
- Marketing landing pages and promotional banners.
- Static legal pages (Privacy Policy, Terms of Service, About Us).
- FAQ and knowledge base management.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- App\Models\User model.

---

## 🗄️ 3. Database Tables Created
- `cms_pages` - Dynamic pages with slug, SEO metadata, and published status.
- `cms_sections` - Reusable layout components and hero banners.

---

## 🛣️ 4. Key Routes
- API: `GET /api/v1/cms/pages/{slug}` - Public page content.
- Admin: `GET /admin/cms` - Page & banner manager.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `PagePublishedEvent`.
- Listens: None.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/CMS` to `app/Modules/`.
2. Register `CMSServiceProvider`.
3. Run `php artisan migrate`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*