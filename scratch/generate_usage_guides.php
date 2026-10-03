<?php

declare(strict_types=1);

$modulesDir = __DIR__ . '/../app/Modules';

$moduleDocs = [
    'AI' => [
        'title' => 'AI Assistant & Automation Module',
        'desc' => 'Provides AI model integrations (OpenAI, Anthropic, Gemini), chat completions, embedding generation, prompt templates, and credit quota management.',
        'use_cases' => [
            'AI Chatbot for customer support or user assistance.',
            'Content generation (blog posts, product descriptions, marketing copy).',
            'AI-driven semantic search, vector embeddings, and text classification.',
            'Quota & token usage tracking per user or subscription plan.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'App\Models\User model with standard authentication.',
            'API Keys in .env: `OPENAI_API_KEY`, `ANTHROPIC_API_KEY`, `GEMINI_API_KEY` (as needed).'
        ],
        'tables' => [
            '`ai_models` - Available AI models, providers, and capabilities.',
            '`ai_prompts` - Reusable prompt templates and system instructions.',
            '`ai_conversations` - User chat threads with AI models.',
            '`ai_messages` - Individual chat messages with token counts.',
            '`ai_usages` - Detailed token consumption and cost tracking per user.'
        ],
        'routes' => [
            'API: `GET /api/v1/ai/models` - List active AI models.',
            'API: `POST /api/v1/ai/chat` - Send prompt and stream/return response.',
            'API: `GET /api/v1/ai/conversations` - User conversation history.',
            'Admin: `GET /admin/ai` - AI usage dashboard and API settings.'
        ],
        'events' => [
            'Dispatches: `AiResponseGeneratedEvent`, `AiQuotaExceededEvent`.',
            'Listens: None (Independent).'
        ],
        'installation' => [
            'Copy `app/Modules/AI` to `app/Modules/` in the new project.',
            'Ensure `app.php` or `bootstrap/providers.php` registers `App\Modules\AI\Providers\AIServiceProvider::class`.',
            'Run migrations: `php artisan migrate`.',
            'Configure API keys in `.env`.'
        ]
    ],
    'ActivityLog' => [
        'title' => 'Activity Log & Audit Trail Module',
        'desc' => 'Universal audit trail capturing user actions, administrative interventions, security events, and model state changes across the entire platform.',
        'use_cases' => [
            'Compliance and regulatory tracking of sensitive user actions.',
            'Admin oversight of staff changes, logins, and permission updates.',
            'Debugging user workflows and identifying security incidents.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'App\Models\User model.'
        ],
        'tables' => [
            '`activity_logs` - Polymorphic log storage (subject_type, subject_id, causer_type, causer_id, properties, IP, user_agent).'
        ],
        'routes' => [
            'API: `GET /api/v1/activities` - User own activity timeline.',
            'Admin: `GET /admin/activities` - Global searchable audit trail with date/user filters.'
        ],
        'events' => [
            'Dispatches: `ActivityLoggedEvent`.',
            'Listens: None (Autonomous).'
        ],
        'installation' => [
            'Copy `app/Modules/ActivityLog` to `app/Modules/`.',
            'Register `App\Modules\ActivityLog\Providers\ActivityLogServiceProvider::class`.',
            'Run `php artisan migrate`.',
            'Use the `LogsActivity` trait on any Eloquent model you wish to track.'
        ]
    ],
    'AdaptiveAuth' => [
        'title' => 'Adaptive & Risk-Based Authentication Module',
        'desc' => 'Evaluates device fingerprints, IP reputation, geolocation anomalies, and velocity checks to dynamically enforce Step-Up MFA or OTP verification.',
        'use_cases' => [
            'Flagging logins from unrecognized devices or foreign countries.',
            'Requiring Step-Up OTP when risky actions are performed (password change, payout request).',
            'Preventing credential stuffing and brute force attacks.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'App\Models\User model.'
        ],
        'tables' => [
            '`user_devices` - Recognized browsers, devices, and trust scores.',
            '`auth_security_logs` - Risk score audits per authentication attempt.'
        ],
        'routes' => [
            'API: `GET /api/v1/security/devices` - User trusted devices list.',
            'API: `POST /api/v1/security/devices/{id}/revoke` - Revoke device trust.',
            'Admin: `GET /admin/security/anomalies` - High risk login incidents.'
        ],
        'events' => [
            'Dispatches: `SuspiciousLoginDetectedEvent`, `DeviceTrustedEvent`.',
            'Listens: `Illuminate\Auth\Events\Login`.'
        ],
        'installation' => [
            'Copy `app/Modules/AdaptiveAuth` to `app/Modules/`.',
            'Register `AdaptiveAuthServiceProvider`.',
            'Run `php artisan migrate`.'
        ]
    ],
    'Affiliate' => [
        'title' => 'Affiliate & Referral Marketing Module',
        'desc' => 'Complete affiliate management system supporting multi-tier commission structures, custom referral codes/links, click tracking, conversion attribution, and wallet cashouts.',
        'use_cases' => [
            'Influencer and partner referral marketing campaigns.',
            'Rewarding customers with commission for inviting colleagues/friends.',
            'Direct instant payout to integrated Wallet balance.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'App\Models\User model.',
            'Payment module (optional, for direct wallet cashout) or manual payouts.'
        ],
        'tables' => [
            '`affiliate_accounts` - User affiliate profiles, codes, commission rates.',
            '`affiliate_referrals` - Tracked visitor clicks and converted registrations.',
            '`affiliate_commissions` - Earned commissions tied to orders or custom conversions.'
        ],
        'routes' => [
            'API: `GET /api/v1/affiliate/dashboard` - Affiliate stats, code, balance.',
            'API: `POST /api/v1/affiliate/payout` - Request cashout to Wallet.',
            'Admin: `GET /admin/affiliates` - Global affiliate management & approvals.'
        ],
        'events' => [
            'Dispatches: `AffiliateCommissionEarnedEvent`, `AffiliatePayoutProcessedEvent`.',
            'Listens: Decoupled via `ModuleNotificationSubscriber` in Notification module.'
        ],
        'installation' => [
            'Copy `app/Modules/Affiliate` to `app/Modules/`.',
            'Register `AffiliateServiceProvider`.',
            'Run `php artisan migrate`.'
        ]
    ],
    'AppSupport' => [
        'title' => 'In-App Support & Bug Reporting Module',
        'desc' => 'Lightweight user feedback, bug report, and customer service ticket system with device telemetry capture (OS, model, app version) and automated system acknowledgment.',
        'use_cases' => [
            'Mobile app and web application bug reporting with telemetry.',
            'Direct customer inquiry desk with admin-to-user thread replies.',
            'Multi-channel notifications (Push, In-App, Email acknowledgment).'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'App\Models\User model.'
        ],
        'tables' => [
            '`app_supports` - Support tickets with OS, app version, status.',
            '`app_support_replies` - Thread messages between user, system, and admin.'
        ],
        'routes' => [
            'API: `POST /api/v1/app-support` - Submit bug or help request.',
            'API: `GET /api/v1/app-support` - List user reports.',
            'API: `POST /api/v1/app-support/{id}/reply` - User thread reply.',
            'Admin: `GET /admin/app-support` - Ticket management dashboard.'
        ],
        'events' => [
            'Dispatches: `AppSupportReportCreatedEvent`, `AppSupportAdminRepliedEvent`, `AppSupportUserRepliedEvent`.',
            'Listens: Decoupled via Notification subscriber.'
        ],
        'installation' => [
            'Copy `app/Modules/AppSupport` to `app/Modules/`.',
            'Register `AppSupportServiceProvider`.',
            'Run `php artisan migrate`.'
        ]
    ],
    'Auth' => [
        'title' => 'Enterprise Authentication & Security Module',
        'desc' => 'Comprehensive authentication system featuring Sanctum tokens, OTP verification (SMS/Email/WhatsApp), brute force lockout, password strength enforcement, and social authentication.',
        'use_cases' => [
            'Secure user registration with NIST-compliant password strength checking.',
            'Multi-channel OTP verification with rate limits and expiry.',
            'Password reset, profile management, and session invalidation.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'Laravel Sanctum, Spatie Permission.'
        ],
        'tables' => [
            '`users` - Core user accounts.',
            '`user_otps` - OTP codes with expiration and brute-force retry counters.'
        ],
        'routes' => [
            'API: `POST /api/v1/auth/register`, `POST /api/v1/auth/login`.',
            'API: `POST /api/v1/auth/otp/verify`, `POST /api/v1/auth/otp/resend`.',
            'API: `POST /api/v1/auth/password/forgot`, `POST /api/v1/auth/password/reset`.'
        ],
        'events' => [
            'Dispatches: `UserRegisteredEvent`, `PasswordResetEvent`, `OtpGeneratedEvent`.',
            'Listens: None.'
        ],
        'installation' => [
            'Core module included in project foundation.',
            'Run `php artisan migrate`.'
        ]
    ],
    'CMS' => [
        'title' => 'Content Management System (CMS) Module',
        'desc' => 'Dynamic pages, landing sections, banners, SEO meta tags, FAQs, and custom content blocks editable from the admin dashboard.',
        'use_cases' => [
            'Marketing landing pages and promotional banners.',
            'Static legal pages (Privacy Policy, Terms of Service, About Us).',
            'FAQ and knowledge base management.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'App\Models\User model.'
        ],
        'tables' => [
            '`cms_pages` - Dynamic pages with slug, SEO metadata, and published status.',
            '`cms_sections` - Reusable layout components and hero banners.'
        ],
        'routes' => [
            'API: `GET /api/v1/cms/pages/{slug}` - Public page content.',
            'Admin: `GET /admin/cms` - Page & banner manager.'
        ],
        'events' => [
            'Dispatches: `PagePublishedEvent`.',
            'Listens: None.'
        ],
        'installation' => [
            'Copy `app/Modules/CMS` to `app/Modules/`.',
            'Register `CMSServiceProvider`.',
            'Run `php artisan migrate`.'
        ]
    ],
    'Call' => [
        'title' => 'Audio & Video Calling Module',
        'desc' => 'WebRTC signaling, Agora / LiveKit token generation, and call session state management for 1-on-1 and group voice/video calls.',
        'use_cases' => [
            'Direct in-app audio and video consultations.',
            'Customer support voice calling.',
            'Team conferences and group channels.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'App\Models\User model.',
            'WebRTC signaling server or Agora credentials in `.env`.'
        ],
        'tables' => [
            '`call_sessions` - Call participants, start/end timestamps, duration, and status.'
        ],
        'routes' => [
            'API: `POST /api/v1/calls/initiate` - Start call and get RTC token.',
            'API: `POST /api/v1/calls/{id}/end` - Terminate call session.'
        ],
        'events' => [
            'Dispatches: `CallInitiatedEvent`, `CallEndedEvent`.',
            'Listens: None.'
        ],
        'installation' => [
            'Copy `app/Modules/Call` to `app/Modules/`.',
            'Register `CallServiceProvider`.',
            'Run `php artisan migrate`.'
        ]
    ],
    'Cart' => [
        'title' => 'Shopping Cart Module',
        'desc' => 'High-performance shopping cart supporting both authenticated users and guest sessions, item options/variants, and automatic subtotal calculation.',
        'use_cases' => [
            'E-commerce shopping cart management.',
            'Guest cart persistence with seamless merge upon user login.',
            'Cart item variant and option selections.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'App\Models\User (optional for guests), Product module (for item details).'
        ],
        'tables' => [
            '`cart_items` - User or session ID, product_id, quantity, options, price.'
        ],
        'routes' => [
            'API: `GET /api/v1/cart` - View current cart.',
            'API: `POST /api/v1/cart/items` - Add item to cart.',
            'API: `PUT /api/v1/cart/items/{id}` - Update quantity.',
            'API: `DELETE /api/v1/cart/items/{id}` - Remove item.'
        ],
        'events' => [
            'Dispatches: `CartItemAddedEvent`, `CartClearedEvent`.',
            'Listens: None.'
        ],
        'installation' => [
            'Copy `app/Modules/Cart` to `app/Modules/`.',
            'Register `CartServiceProvider`.',
            'Run `php artisan migrate`.'
        ]
    ],
    'Chat' => [
        'title' => 'Real-Time Chat & Messaging Module',
        'desc' => 'Full-featured messaging system supporting 1-on-1 direct messaging, public/private groups, channels, multimedia attachments, user blocking, and room settings.',
        'use_cases' => [
            'Customer-to-vendor marketplace messaging.',
            'Team chat rooms and community channels.',
            'User safety with block lists and message moderation.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'App\Models\User model, Laravel Reverb or Pusher (for real-time broadcasting).'
        ],
        'tables' => [
            '`chat_rooms` - Direct, group, and channel chat rooms.',
            '`chat_participants` - Members, roles (admin, member), mute states.',
            '`chat_messages` - Text, media attachments, read receipts.',
            '`chat_blocks` - User block lists.'
        ],
        'routes' => [
            'API: `GET /api/v1/chat/rooms` - User active chats.',
            'API: `POST /api/v1/chat/messages` - Send message.',
            'API: `POST /api/v1/chat/rooms` - Create group/direct room.'
        ],
        'events' => [
            'Dispatches: `MessageSentEvent`, `ParticipantJoinedEvent`.',
            'Listens: None.'
        ],
        'installation' => [
            'Copy `app/Modules/Chat` to `app/Modules/`.',
            'Register `ChatServiceProvider`.',
            'Run `php artisan migrate`.'
        ]
    ],
    'Coupon' => [
        'title' => 'Discounts & Coupon Promotion Module',
        'desc' => 'Flexible promotional engine supporting fixed or percentage discounts, minimum cart thresholds, per-user usage limits, and category restrictions.',
        'use_cases' => [
            'Promo codes for marketing campaigns (e.g. SUMMER20).',
            'First-time order coupons.',
            'Vendor-specific or category-specific discount vouchers.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'App\Models\User model.'
        ],
        'tables' => [
            '`coupons` - Code, discount type, value, expiry date, usage limit.',
            '`coupon_usages` - Audit trail of user redemptions.'
        ],
        'routes' => [
            'API: `POST /api/v1/coupons/apply` - Validate and calculate discount.',
            'Admin: `GET /admin/coupons` - Coupon management.'
        ],
        'events' => [
            'Dispatches: `CouponAppliedEvent`.',
            'Listens: None.'
        ],
        'installation' => [
            'Copy `app/Modules/Coupon` to `app/Modules/`.',
            'Register `CouponServiceProvider`.',
            'Run `php artisan migrate`.'
        ]
    ],
    'Interaction' => [
        'title' => 'Social Interaction & Engagement Module',
        'desc' => 'Universal polymorphic interaction engine supporting nested comments, replies, likes, bookmarks, shareable links, and view counters with anti-spam cooldown.',
        'use_cases' => [
            'Product reviews, questions & answers, comments.',
            'Post discussions and blog replies.',
            'User wishlists and bookmarking of any entity.',
            'SEO-friendly public and expiring private share links.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'App\Models\User model.'
        ],
        'tables' => [
            '`interaction_comments` - Nested polymorphic comments and replies.',
            '`interaction_likes` - Polymorphic like toggles.',
            '`interaction_bookmarks` - User saved items and wishlists.',
            '`interaction_shares` - Share links with optional password and expiry.',
            '`interaction_views` - View logs with IP cooldown.'
        ],
        'routes' => [
            'API: `POST /api/v1/interactions/comments` - Post comment/reply.',
            'API: `POST /api/v1/interactions/like` - Toggle like.',
            'API: `POST /api/v1/interactions/bookmark` - Save bookmark.'
        ],
        'events' => [
            'Dispatches: `CommentCreatedEvent`, `LikeToggledEvent`.',
            'Listens: None.'
        ],
        'installation' => [
            'Copy `app/Modules/Interaction` to `app/Modules/`.',
            'Register `InteractionServiceProvider`.',
            'Run `php artisan migrate`.'
        ]
    ],
    'Media' => [
        'title' => 'Universal Media & File Management Module',
        'desc' => 'Centralized file storage, image resizing, secure CDN uploading, and polymorphic media association across any application model.',
        'use_cases' => [
            'User avatar uploads and document attachments.',
            'Product image galleries and downloadable digital assets.',
            'Ticket and support attachments.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'Intervention Image (optional for manipulation).'
        ],
        'tables' => [
            '`media` - Polymorphic media table (model_type, model_id, file_path, disk, mime_type, size).'
        ],
        'routes' => [
            'API: `POST /api/v1/media/upload` - Direct file upload.',
            'API: `DELETE /api/v1/media/{id}` - Delete file.'
        ],
        'events' => [
            'Dispatches: `MediaUploadedEvent`, `MediaDeletedEvent`.',
            'Listens: None.'
        ],
        'installation' => [
            'Copy `app/Modules/Media` to `app/Modules/`.',
            'Register `MediaServiceProvider`.',
            'Run `php artisan migrate`.'
        ]
    ],
    'Notification' => [
        'title' => 'Multi-Channel Notification & Push Engine',
        'desc' => 'Decoupled, event-driven notification hub supporting In-App database alerts, Firebase Cloud Messaging (FCM) mobile push, Email, and WebSockets (Pusher/Reverb).',
        'use_cases' => [
            'Automated notifications for orders, tickets, sales, and reviews.',
            'Admin broadcast notifications and marketing campaigns.',
            'Real-time badge counter and in-app notification center.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'App\Models\User model, Firebase credentials (for FCM push).'
        ],
        'tables' => [
            '`notifications` - Multi-channel notification store with read receipts and metadata.'
        ],
        'routes' => [
            'API: `GET /api/v1/notifications` - Paginated user notifications.',
            'API: `POST /api/v1/notifications/{id}/read` - Mark notification as read.',
            'Admin: `POST /admin/notifications/broadcast` - Bulk notification push.'
        ],
        'events' => [
            'Dispatches: `NotificationCreated`.',
            'Listens: Subscribes via `ModuleNotificationSubscriber` to Ticket, Review, Vendor, Affiliate, and AppSupport events.'
        ],
        'installation' => [
            'Copy `app/Modules/Notification` to `app/Modules/`.',
            'Register `NotificationServiceProvider`.',
            'Run `php artisan migrate`.'
        ]
    ],
    'Order' => [
        'title' => 'E-Commerce Order & Checkout State Machine',
        'desc' => 'Mission-critical order processing module featuring atomic inventory decrement, strict state machine transitions (Pending -> Processing -> Shipped -> Delivered / Cancelled), and inventory restoration.',
        'use_cases' => [
            'Cart checkout with atomic database transactions to avoid race conditions.',
            'Order state management and tracking.',
            'Cancellation with automatic stock restoration.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'Product module, Cart module (or direct items payload).'
        ],
        'tables' => [
            '`orders` - Order header, totals, shipping/billing address, payment and fulfillment status.',
            '`order_items` - Line items, prices, quantities, product snapshots.'
        ],
        'routes' => [
            'API: `POST /api/v1/orders/checkout` - Atomic order creation.',
            'API: `GET /api/v1/orders` - User order history.',
            'API: `POST /api/v1/orders/{id}/cancel` - Cancel order and restore stock.',
            'Admin: `GET /admin/orders` - Order fulfillment dashboard.'
        ],
        'events' => [
            'Dispatches: `OrderCreatedEvent`, `OrderStatusUpdatedEvent`, `OrderCancelledEvent`.',
            'Listens: None.'
        ],
        'installation' => [
            'Copy `app/Modules/Order` to `app/Modules/`.',
            'Register `OrderServiceProvider`.',
            'Run `php artisan migrate`.'
        ]
    ],
    'Payment' => [
        'title' => 'Multi-Gateway Payment & Digital Wallet Module',
        'desc' => 'Dual payment architecture: Digital Wallet with P2P transfers, deposits, withdrawals, and Gateway adapters (Stripe, SSLCommerz, PayPal, Razorpay, Mock).',
        'use_cases' => [
            'Digital wallet balance for instant checkouts, earnings, and cashouts.',
            'P2P funds transfer between registered users.',
            'Credit card, Mobile Money, and bank gateway integrations.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'App\Models\User model.'
        ],
        'tables' => [
            '`wallets` - User balance, currency, status.',
            '`wallet_transactions` - Ledger of credits, debits, transfers.',
            '`payment_gateways` - Active payment processors and API keys.'
        ],
        'routes' => [
            'API: `GET /api/v1/wallet/balance` - User wallet summary.',
            'API: `POST /api/v1/wallet/transfer` - Send funds to another user.',
            'API: `POST /api/v1/payments/initiate` - Start gateway payment.'
        ],
        'events' => [
            'Dispatches: `WalletDepositedEvent`, `TransferCompletedEvent`, `PaymentSuccessEvent`.',
            'Listens: None.'
        ],
        'installation' => [
            'Copy `app/Modules/Payment` to `app/Modules/`.',
            'Register `PaymentServiceProvider`.',
            'Run `php artisan migrate`.'
        ]
    ],
    'Post' => [
        'title' => 'Blog & Article Publishing Module',
        'desc' => 'Complete content publication system supporting rich text articles, authors, categories, tags, SEO optimization, and scheduled publishing.',
        'use_cases' => [
            'Company blog, news releases, and announcements.',
            'Knowledge articles and tutorials.',
            'Content marketing and SEO articles.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'App\Models\User model.'
        ],
        'tables' => [
            '`posts` - Title, slug, content, featured image, published status, SEO tags.',
            '`post_categories` - Post taxonomies.'
        ],
        'routes' => [
            'API: `GET /api/v1/posts` - List published articles.',
            'API: `GET /api/v1/posts/{slug}` - Read article.',
            'Admin: `GET /admin/posts` - Blog management.'
        ],
        'events' => [
            'Dispatches: `PostPublishedEvent`.',
            'Listens: None.'
        ],
        'installation' => [
            'Copy `app/Modules/Post` to `app/Modules/`.',
            'Register `PostServiceProvider`.',
            'Run `php artisan migrate`.'
        ]
    ],
    'Product' => [
        'title' => 'Product Catalog & Inventory Module',
        'desc' => 'E-commerce catalog engine supporting physical and digital products, SKU variations, inventory stock tracking, pricing, and category hierarchies.',
        'use_cases' => [
            'Product listings with filtering by category, brand, and price.',
            'Inventory control with atomic stock reservation.',
            'Multi-vendor product management.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'App\Models\User model.'
        ],
        'tables' => [
            '`products` - Title, slug, description, price, stock, SKU, status.',
            '`categories` - Multi-level category tree.'
        ],
        'routes' => [
            'API: `GET /api/v1/products` - Filterable catalog list.',
            'API: `GET /api/v1/products/{slug}` - Product detail.',
            'Admin: `GET /admin/products` - Inventory catalog manager.'
        ],
        'events' => [
            'Dispatches: `ProductCreatedEvent`, `StockLevelChangedEvent`.',
            'Listens: None.'
        ],
        'installation' => [
            'Copy `app/Modules/Product` to `app/Modules/`.',
            'Register `ProductServiceProvider`.',
            'Run `php artisan migrate`.'
        ]
    ],
    'Review' => [
        'title' => 'Ratings & Customer Reviews Module',
        'desc' => 'Decoupled polymorphic rating engine supporting 1-5 star reviews, media attachments, helpfulness voting, admin/vendor replies, and moderation.',
        'use_cases' => [
            'Customer product reviews and ratings.',
            'Vendor store evaluations.',
            'Helpful/unhelpful community voting.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'App\Models\User model.'
        ],
        'tables' => [
            '`reviews` - Rating (1-5), comment, verified purchase flag, status.',
            '`review_votes` - Community helpful/unhelpful upvotes.'
        ],
        'routes' => [
            'API: `GET /api/v1/reviews` - List approved reviews with score summary.',
            'API: `POST /api/v1/reviews` - Submit review with rating and photos.',
            'API: `POST /api/v1/reviews/{id}/vote` - Upvote helpfulness.',
            'API: `POST /api/v1/reviews/{id}/reply` - Vendor or staff reply.'
        ],
        'events' => [
            'Dispatches: `ReviewSubmittedEvent`, `ReviewRepliedEvent`.',
            'Listens: Decoupled via Notification module.'
        ],
        'installation' => [
            'Copy `app/Modules/Review` to `app/Modules/`.',
            'Register `ReviewServiceProvider`.',
            'Run `php artisan migrate`.'
        ]
    ],
    'Reward' => [
        'title' => 'Gamification, Loyalty Points & Badges Module',
        'desc' => 'Customer retention suite offering loyalty points earning, daily check-in streaks, Tier promotion (Bronze -> Silver -> Gold -> Platinum), unlockable badges, and cash redemption to Wallet.',
        'use_cases' => [
            'Points rewarded on purchases, reviews, and daily app visits.',
            'Automatic VIP tier promotions with perks.',
            'Points redemption into direct Wallet cash.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'App\Models\User model.'
        ],
        'tables' => [
            '`reward_tiers` - Tier definitions and threshold points.',
            '`reward_user_accounts` - User current points, tier, and check-in streak.',
            '`reward_badges` - Earnable badges and user achievements.',
            '`reward_transactions` - Ledger of points earned and redeemed.'
        ],
        'routes' => [
            'API: `GET /api/v1/rewards/overview` - User points and tier.',
            'API: `POST /api/v1/rewards/daily-checkin` - Claim daily check-in points.',
            'API: `POST /api/v1/rewards/redeem` - Convert points to wallet cash.'
        ],
        'events' => [
            'Dispatches: `PointsEarnedEvent`, `TierPromotedEvent`, `BadgeUnlockedEvent`.',
            'Listens: None.'
        ],
        'installation' => [
            'Copy `app/Modules/Reward` to `app/Modules/`.',
            'Register `RewardServiceProvider`.',
            'Run `php artisan migrate`.'
        ]
    ],
    'Social' => [
        'title' => 'Social OAuth Authentication Module',
        'desc' => 'Plug-and-play Socialite login integration for Google, Facebook, Apple, and GitHub with automated user account matching and profile syncing.',
        'use_cases' => [
            'One-click sign-in with Google, Facebook, Apple, or GitHub.',
            'Linking social identities to existing user accounts.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'Laravel Socialite package, App\Models\User model.'
        ],
        'tables' => [
            '`social_accounts` - Provider (google, facebook), provider_user_id, user_id, tokens.'
        ],
        'routes' => [
            'Web: `GET /auth/{provider}/redirect` - Redirect to OAuth provider.',
            'Web: `GET /auth/{provider}/callback` - Handle callback and log in user.'
        ],
        'events' => [
            'Dispatches: `SocialAccountLinkedEvent`.',
            'Listens: None.'
        ],
        'installation' => [
            'Copy `app/Modules/Social` to `app/Modules/`.',
            'Register `SocialServiceProvider`.',
            'Add provider client IDs & secrets to `config/services.php` and `.env`.'
        ]
    ],
    'Subscription' => [
        'title' => 'SaaS Subscription & Feature Quota Module',
        'desc' => 'Subscription management engine supporting recurring tiers (Free, Monthly, Yearly), feature entitlement checks, usage quota consumption, and wallet payments.',
        'use_cases' => [
            'SaaS pricing tiers with feature flags and usage limits.',
            'Automated renewal and expiration handling.',
            'Instant subscription purchase via integrated Wallet.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'App\Models\User model.'
        ],
        'tables' => [
            '`subscription_plans` - Pricing, billing period, quotas, feature flags.',
            '`user_subscriptions` - Active subscription records, start/end dates, renewal status.'
        ],
        'routes' => [
            'API: `GET /api/v1/subscriptions/plans` - Public pricing plans.',
            'API: `POST /api/v1/subscriptions/subscribe` - Purchase subscription plan.',
            'API: `POST /api/v1/subscriptions/cancel` - Cancel active subscription.'
        ],
        'events' => [
            'Dispatches: `SubscriptionCreatedEvent`, `SubscriptionCancelledEvent`.',
            'Listens: None.'
        ],
        'installation' => [
            'Copy `app/Modules/Subscription` to `app/Modules/`.',
            'Register `SubscriptionServiceProvider`.',
            'Run `php artisan migrate`.'
        ]
    ],
    'Ticket' => [
        'title' => 'Customer Support Helpdesk & Ticketing Module',
        'desc' => 'Full-lifecycle customer support ticketing system with categories, priority levels, attachments, internal staff notes, agent assignment, and domain event notifications.',
        'use_cases' => [
            'Helpdesk ticketing with multi-party conversations (Customer, Staff, Admin).',
            'Internal staff-only private notes.',
            'Agent ticket routing and assignment.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'App\Models\User model.'
        ],
        'tables' => [
            '`ticket_categories` - Support departments and categories.',
            '`tickets` - Ticket header with unique ticket_number, priority, status.',
            '`ticket_messages` - Conversation replies and private staff notes.'
        ],
        'routes' => [
            'API: `GET /api/v1/tickets` - List user tickets.',
            'API: `POST /api/v1/tickets` - Submit new support ticket.',
            'API: `POST /api/v1/tickets/{id}/reply` - Reply to ticket.',
            'API: `POST /api/v1/tickets/{id}/close` - Close ticket.',
            'Admin: `GET /admin/tickets` - Helpdesk agent dashboard.'
        ],
        'events' => [
            'Dispatches: `TicketCreatedEvent`, `TicketRepliedEvent`, `TicketAssignedEvent`, `TicketStatusUpdatedEvent`.',
            'Listens: Decoupled via Notification module.'
        ],
        'installation' => [
            'Copy `app/Modules/Ticket` to `app/Modules/`.',
            'Register `TicketServiceProvider`.',
            'Run `php artisan migrate`.'
        ]
    ],
    'Vendor' => [
        'title' => 'Multi-Vendor Marketplace & Store Module',
        'desc' => 'Comprehensive multi-vendor engine supporting store registration, staff roles & permissions, commission rate calculations, sales revenue splits, and payout requests to wallet.',
        'use_cases' => [
            'Multi-vendor e-commerce marketplace stores.',
            'Commission fee splits on completed sales.',
            'Vendor payout cashout directly to Wallet.'
        ],
        'requirements' => [
            'PHP 8.2+, Laravel 11+',
            'App\Models\User model, Payment module (for wallet payout).'
        ],
        'tables' => [
            '`vendor_stores` - Store profile, commission rate, balance, total earnings.',
            '`vendor_members` - Store staff members and permission roles.',
            '`vendor_payouts` - Withdrawal and payout transaction requests.'
        ],
        'routes' => [
            'API: `GET /api/v1/vendors` - Directory of active vendor stores.',
            'API: `POST /api/v1/vendors/register` - Apply for vendor store.',
            'API: `GET /api/v1/vendors/my-store` - Vendor dashboard summary.',
            'API: `POST /api/v1/vendors/payout` - Request store balance payout.',
            'Admin: `GET /admin/vendors` - Global vendor store approvals.'
        ],
        'events' => [
            'Dispatches: `VendorStoreRegisteredEvent`, `VendorSaleRecordedEvent`, `VendorPayoutRequestedEvent`, `VendorStatusUpdatedEvent`.',
            'Listens: Decoupled via Notification module.'
        ],
        'installation' => [
            'Copy `app/Modules/Vendor` to `app/Modules/`.',
            'Register `VendorServiceProvider`.',
            'Run `php artisan migrate`.'
        ]
    ]
];

$count = 0;
foreach ($moduleDocs as $module => $doc) {
    $filePath = "{$modulesDir}/{$module}/USAGE.md";
    
    $useCasesList = implode("\n", array_map(fn($item) => "- {$item}", $doc['use_cases']));
    $reqList = implode("\n", array_map(fn($item) => "- {$item}", $doc['requirements']));
    $tablesList = implode("\n", array_map(fn($item) => "- {$item}", $doc['tables']));
    $routesList = implode("\n", array_map(fn($item) => "- {$item}", $doc['routes']));
    $eventsList = implode("\n", array_map(fn($item) => "- {$item}", $doc['events']));
    $installList = implode("\n", array_map(fn($idx, $item) => ($idx + 1) . ". {$item}", array_keys($doc['installation']), $doc['installation']));

    $content = <<<MD
# {$doc['title']} (`{$module}`)

{$doc['desc']}

---

## 🎯 1. Use Cases
{$useCasesList}

---

## 📋 2. Requirements & Prerequisites
{$reqList}

---

## 🗄️ 3. Database Tables Created
{$tablesList}

---

## 🛣️ 4. Key Routes
{$routesList}

---

## ⚡ 5. Events & Decoupled Architecture
{$eventsList}

---

## 🚀 6. Plug & Play Installation Guide
To use this module in any other Laravel project:
{$installList}

---
*Generated as part of the modular, plug-and-play architecture for One-Dashboard.*
MD;

    file_put_contents($filePath, $content);
    $count++;
}

echo "Successfully generated USAGE.md for {$count} modules!\n";
