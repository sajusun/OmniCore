<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Modules\Notification\Models\Notification;

$user = User::find(1);

if (!$user) {
    echo "User with ID 1 does not exist in the database! Creating user 1...\n";
    $user = User::create([
        'id'       => 1,
        'name'     => 'Super Admin',
        'email'    => 'admin@example.com',
        'password' => bcrypt('Password123!@#'),
    ]);
}

echo "Found User: {$user->name} (ID: {$user->id}, Email: {$user->email})\n";

// Clear previous test notifications for user 1 to avoid clutter (optional, let's keep or add fresh ones)
echo "Seeding 14 realistic notifications for User ID {$user->id}...\n";

$sampleNotifications = [
    [
        'title'   => 'New Vendor Order Received! 🛍️',
        'body'    => 'Customer John Doe placed an order #ORD-84920 amounting to $249.99 for Apple AirPods Pro.',
        'type'    => 'order',
        'link'    => '/admin/orders',
        'read_at' => null,
        'created' => now()->subMinutes(3),
    ],
    [
        'title'   => 'Support Ticket Assigned to You 🎫',
        'body'    => 'Ticket #SUP-2026-9041 "Payment gateway webhook failing on checkout" was assigned to your queue.',
        'type'    => 'ticket',
        'link'    => '/admin/tickets',
        'read_at' => null,
        'created' => now()->subMinutes(12),
    ],
    [
        'title'   => 'Payout Request Approved 💰',
        'body'    => 'Your vendor store payout of $1,450.00 has been transferred directly into your Digital Wallet.',
        'type'    => 'wallet',
        'link'    => '/admin/wallet',
        'read_at' => null,
        'created' => now()->subHours(1),
    ],
    [
        'title'   => 'New 5-Star Customer Review ⭐⭐⭐⭐⭐',
        'body'    => 'Sarah Jenkins reviewed "Wireless Ergonomic Keyboard": "Best keyboard I have ever used. Fast delivery!"',
        'type'    => 'review',
        'link'    => '/admin/reviews',
        'read_at' => null,
        'created' => now()->subHours(2),
    ],
    [
        'title'   => 'Security Alert: New Login from London 🛡️',
        'body'    => 'A login attempt from Chrome 129 on Windows was authenticated using Step-Up MFA OTP.',
        'type'    => 'security',
        'link'    => '/admin/security/devices',
        'read_at' => null,
        'created' => now()->subHours(4),
    ],
    [
        'title'   => 'Affiliate Commission Credited 🎉',
        'body'    => 'Referral code REF-OMNI earned you $45.00 commission from a converted SaaS Subscription.',
        'type'    => 'affiliate',
        'link'    => '/admin/affiliates',
        'read_at' => null,
        'created' => now()->subHours(6),
    ],
    [
        'title'   => 'System Backup Completed Successfully 💾',
        'body'    => 'Scheduled daily database backup `backup_2026_10_03.sql.gz` archived to AWS S3 storage.',
        'type'    => 'system',
        'link'    => null,
        'read_at' => now()->subHours(8),
    ],
    [
        'title'   => 'Low Inventory Warning ⚠️',
        'body'    => 'Product "Logitech MX Master 3S" stock level has dropped below the threshold of 5 units.',
        'type'    => 'product',
        'link'    => '/admin/products',
        'read_at' => null,
        'created' => now()->subHours(12),
    ],
    [
        'title'   => 'VIP Tier Promotion Unlocked! 🏆',
        'body'    => 'Congratulations! Your account reached Platinum Tier with 5,000 loyalty points.',
        'type'    => 'reward',
        'link'    => '/admin/rewards',
        'read_at' => now()->subDay(),
    ],
    [
        'title'   => 'New Vendor Store Application 🏪',
        'body'    => 'Vendor "TechHub Electronics" submitted their trade license documents for store verification.',
        'type'    => 'vendor',
        'link'    => '/admin/vendors',
        'read_at' => null,
        'created' => now()->subDays(2),
    ],
    // Batch for testing "Load More" cursor pagination
    [
        'title'   => 'Subscription Renewal Reminder 📅',
        'body'    => 'Enterprise Plan subscription renews in 3 days. Your auto-renewal method is active.',
        'type'    => 'subscription',
        'link'    => '/admin/subscriptions',
        'read_at' => now()->subDays(3),
    ],
    [
        'title'   => 'App Bug Report Received 🐛',
        'body'    => 'User submitted bug report #SUP-BUG-109: "Dark mode toggle flickering in mobile safari".',
        'type'    => 'app_support',
        'link'    => '/admin/app-support',
        'read_at' => now()->subDays(4),
    ],
    [
        'title'   => 'Coupon "FLASH50" Expired 🏷️',
        'body'    => 'Promotional coupon code FLASH50 has expired after reaching 500 maximum redemptions.',
        'type'    => 'coupon',
        'link'    => '/admin/coupons',
        'read_at' => now()->subDays(5),
    ],
    [
        'title'   => 'Welcome to OmniCore Dashboard! 🚀',
        'body'    => 'Your modular admin dashboard is ready with 24 plug-and-play decoupled enterprise modules.',
        'type'    => 'welcome',
        'link'    => null,
        'read_at' => now()->subDays(7),
    ],
];

foreach ($sampleNotifications as $item) {
    Notification::create([
        'user_id'    => $user->id,
        'title'      => $item['title'],
        'body'       => $item['body'],
        'type'       => $item['type'],
        'link'       => $item['link'],
        'read_at'    => $item['read_at'],
        'created_at' => $item['created'] ?? now(),
    ]);
}

$unread = Notification::where('user_id', $user->id)->whereNull('read_at')->count();
$total = Notification::where('user_id', $user->id)->count();

echo "Successfully seeded notifications!\n";
echo "Total Notifications: {$total}\n";
echo "Unread Notifications: {$unread}\n";
