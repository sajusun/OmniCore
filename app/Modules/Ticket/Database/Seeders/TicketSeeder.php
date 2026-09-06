<?php

declare(strict_types=1);

namespace App\Modules\Ticket\Database\Seeders;

use App\Modules\Ticket\Models\CannedResponse;
use App\Modules\Ticket\Models\TicketCategory;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'General Inquiries',
                'slug'        => 'general-inquiries',
                'description' => 'General questions about platform services and account.',
                'icon'        => 'fa fa-info-circle',
                'is_active'   => true,
                'sort_order'  => 1,
            ],
            [
                'name'        => 'Billing & Payments',
                'slug'        => 'billing-payments',
                'description' => 'Questions about wallet, transactions, subscriptions, and invoices.',
                'icon'        => 'fa fa-credit-card',
                'is_active'   => true,
                'sort_order'  => 2,
            ],
            [
                'name'        => 'Technical & Bug Reports',
                'slug'        => 'technical-bugs',
                'description' => 'Report technical glitches, app crashes, or unexpected behavior.',
                'icon'        => 'fa fa-bug',
                'is_active'   => true,
                'sort_order'  => 3,
            ],
            [
                'name'        => 'Feature Requests & Feedback',
                'slug'        => 'feature-requests',
                'description' => 'Suggest new features and improvement ideas.',
                'icon'        => 'fa fa-lightbulb-o',
                'is_active'   => true,
                'sort_order'  => 4,
            ],
        ];

        foreach ($categories as $cat) {
            TicketCategory::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        $cannedResponses = [
            [
                'title'     => 'Greeting & Acknowledgment',
                'shortcut'  => '!greet',
                'body'      => "Hello,\n\nThank you for reaching out to our support team. We have received your request and our team is actively reviewing the details. We will update you shortly.",
                'is_active' => true,
            ],
            [
                'title'     => 'Request for More Information',
                'shortcut'  => '!info',
                'body'      => "Hello,\n\nTo help us investigate and resolve this issue as quickly as possible, could you please provide additional details or screenshots of the issue?",
                'is_active' => true,
            ],
            [
                'title'     => 'Issue Resolved & Closing',
                'shortcut'  => '!resolved',
                'body'      => "Hello,\n\nWe have verified that the issue has now been resolved. Please test it on your end and let us know if you need any further assistance!\n\nBest regards,\nSupport Team",
                'is_active' => true,
            ],
        ];

        foreach ($cannedResponses as $cr) {
            CannedResponse::updateOrCreate(['shortcut' => $cr['shortcut']], $cr);
        }
    }
}
