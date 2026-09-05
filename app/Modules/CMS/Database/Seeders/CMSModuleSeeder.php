<?php

namespace App\Modules\CMS\Database\Seeders;

use App\Modules\CMS\Models\CMS;
use Illuminate\Database\Seeder;

class CMSModuleSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            [
                'page'        => 'home',
                'section'     => 'hero',
                'title'       => 'Build Fast, Scale Effortlessly',
                'description' => 'The ultimate enterprise-grade modular Laravel starter platform with built-in e-commerce, real-time chat, and clean APIs.',
                'status'      => 'active',
            ],
            [
                'page'        => 'about-us',
                'section'     => 'our-story',
                'title'       => 'Our Mission & Vision',
                'description' => 'We build industry-standard software designed for productivity, maintainability, and exceptional developer experience.',
                'status'      => 'active',
            ],
            [
                'page'        => 'privacy-policy',
                'section'     => 'policy',
                'title'       => 'Privacy Policy',
                'description' => 'We value your privacy and are committed to protecting your personal data using encryption and strict security standards.',
                'status'      => 'active',
            ],
            [
                'page'        => 'terms-conditions',
                'section'     => 'terms',
                'title'       => 'Terms and Conditions',
                'description' => 'By using this platform, you agree to our standard terms and conditions of service.',
                'status'      => 'active',
            ],
        ];

        foreach ($sections as $sec) {
            CMS::firstOrCreate(
                ['page' => $sec['page'], 'section' => $sec['section']],
                $sec
            );
        }

        echo "✓ CMS Module Seeding Complete!\n";
    }
}
