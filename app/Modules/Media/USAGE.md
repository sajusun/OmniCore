# Universal Media & File Management Module (`Media`)

Centralized file storage, image resizing, secure CDN uploading, and polymorphic media association across any application model.

---

## 🎯 1. Use Cases
- User avatar uploads and document attachments.
- Product image galleries and downloadable digital assets.
- Ticket and support attachments.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- Intervention Image (optional for manipulation).

---

## 🗄️ 3. Database Tables Created
- `media` - Polymorphic media table (model_type, model_id, file_path, disk, mime_type, size).

---

## 🛣️ 4. Key Routes
- API: `POST /api/v1/media/upload` - Direct file upload.
- API: `DELETE /api/v1/media/{id}` - Delete file.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `MediaUploadedEvent`, `MediaDeletedEvent`.
- Listens: None.

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/Media` to `app/Modules/`.
2. Register `MediaServiceProvider`.
3. Run `php artisan migrate`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*