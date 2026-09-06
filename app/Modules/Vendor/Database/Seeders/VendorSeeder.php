<?php

declare(strict_types=1);

namespace App\Modules\Vendor\Database\Seeders;

use App\Models\User;
use App\Modules\Vendor\Enums\VendorStatus;
use App\Modules\Vendor\Models\VendorMember;
use App\Modules\Vendor\Models\VendorStore;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if ($user) {
            $store = VendorStore::firstOrCreate(
                ['slug' => 'apex-official-store'],
                [
                    'user_id'         => $user->id,
                    'name'            => 'Apex Official Flagship Store',
                    'description'     => 'Authentic premium tech gadgets, accessories, and audio gear.',
                    'phone'           => '+1 (555) 019-2834',
                    'email'           => 'support@apexstore.example.com',
                    'address'         => '100 Silicon Ave, Suite 400, San Francisco, CA',
                    'commission_rate' => 8.50,
                    'status'          => VendorStatus::ACTIVE->value,
                    'total_sales'     => 12500.00,
                    'total_earnings'  => 11437.50,
                    'balance'         => 3200.00,
                    'is_featured'     => true,
                ]
            );

            VendorMember::firstOrCreate(
                ['vendor_id' => $store->id, 'user_id' => $user->id],
                ['role' => 'owner', 'permissions' => ['all']]
            );
        }
    }
}
