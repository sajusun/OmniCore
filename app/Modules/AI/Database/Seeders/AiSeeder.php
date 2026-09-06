<?php

namespace App\Modules\AI\Database\Seeders;

use App\Modules\AI\Models\AiKnowledgeBase;
use Illuminate\Database\Seeder;

class AiSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'category' => 'Orders & Shipping',
                'question' => 'How can I track my order?',
                'answer' => 'You can track your order in real-time by navigating to "My Orders" in your dashboard and clicking on the "Track Order" button next to your shipment.',
                'keywords' => ['track', 'order', 'shipping', 'delivery', 'where is my package'],
                'tags' => ['orders', 'shipping'],
            ],
            [
                'category' => 'Returns & Refunds',
                'question' => 'What is your return policy?',
                'answer' => 'We offer a 30-day hassle-free return policy on all eligible items. Ensure the product is in original condition with packaging intact. Submit a return request through your Order History page.',
                'keywords' => ['return', 'refund', 'money back', 'exchange', 'policy'],
                'tags' => ['returns', 'refunds'],
            ],
            [
                'category' => 'Payments & Wallet',
                'question' => 'How do I withdraw funds from my wallet?',
                'answer' => 'Go to the Wallet section, click "Withdraw Funds", select your preferred payment gateway (Stripe/PayPal/Bank), and enter the desired amount. Standard processing takes 24-48 business hours.',
                'keywords' => ['wallet', 'withdraw', 'payout', 'balance', 'cashout'],
                'tags' => ['wallet', 'payouts'],
            ],
            [
                'category' => 'Affiliate & Rewards',
                'question' => 'How do I earn affiliate commissions?',
                'answer' => 'Join our Affiliate Program from your dashboard, grab your unique referral link, and share it with your audience. You will earn automatic percentage commissions on every completed purchase.',
                'keywords' => ['affiliate', 'referral', 'commission', 'earn', 'partner'],
                'tags' => ['affiliate', 'rewards'],
            ],
            [
                'category' => 'Vendor Stores',
                'question' => 'How can I become a verified seller/vendor?',
                'answer' => 'Click "Open a Store" in your settings, fill in your business and tax details, upload your identity verification documents, and our merchant team will review your application within 24 hours.',
                'keywords' => ['vendor', 'seller', 'store', 'sell', 'merchant'],
                'tags' => ['vendor', 'marketplace'],
            ],
        ];

        foreach ($faqs as $faq) {
            AiKnowledgeBase::updateOrCreate(
                ['question' => $faq['question']],
                $faq
            );
        }
    }
}
