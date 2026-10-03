# AI Assistant & Automation Module (`AI`)

Provides AI model integrations (OpenAI, Anthropic, Gemini), chat completions, embedding generation, prompt templates, and credit quota management.

---

## 🎯 1. Use Cases
- AI Chatbot for customer support or user assistance.
- Content generation (blog posts, product descriptions, marketing copy).
- AI-driven semantic search, vector embeddings, and text classification.
- Quota & token usage tracking per user or subscription plan.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- App\Models\User model with standard authentication.
- API Keys in .env: `OPENAI_API_KEY`, `ANTHROPIC_API_KEY`, `GEMINI_API_KEY` (as needed).

---

## 🗄️ 3. Database Tables Created
- `ai_models` - Available AI models, providers, and capabilities.
- `ai_prompts` - Reusable prompt templates and system instructions.
- `ai_conversations` - User chat threads with AI models.
- `ai_messages` - Individual chat messages with token counts.
- `ai_usages` - Detailed token consumption and cost tracking per user.

---

## 🛣️ 4. Key Routes
- API: `GET /api/v1/ai/models` - List active AI models.
- API: `POST /api/v1/ai/chat` - Send prompt and stream/return response.
- API: `GET /api/v1/ai/conversations` - User conversation history.
- Admin: `GET /admin/ai` - AI usage dashboard and API settings.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: `AiResponseGeneratedEvent`, `AiQuotaExceededEvent`.
- Listens: None (Independent).

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
1. Copy `app/Modules/AI` to `app/Modules/` in the new project.
2. Ensure `app.php` or `bootstrap/providers.php` registers `App\Modules\AI\Providers\AIServiceProvider::class`.
3. Run migrations: `php artisan migrate`.
4. Configure API keys in `.env`.

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*