<?php

namespace App\Modules\Order\Database\Seeders;

use App\Modules\Coupon\Models\Coupon;
use App\Modules\Order\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class OrderModuleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Coupons
        Coupon::firstOrCreate(
            ['code' => 'WELCOME10'],
            [
                'type' => 'percentage',
                'value' => 10,
                'min_order_amount' => 50.00,
                'max_discount_amount' => 25.00,
                'usage_limit' => 1000,
                'usage_limit_per_user' => 1,
                'is_active' => true,
            ]
        );

        Coupon::firstOrCreate(
            ['code' => 'SAVE20'],
            [
                'type' => 'percentage',
                'value' => 20,
                'min_order_amount' => 100.00,
                'max_discount_amount' => 50.00,
                'usage_limit' => 500,
                'usage_limit_per_user' => 1,
                'is_active' => true,
            ]
        );

        Coupon::firstOrCreate(
            ['code' => 'FLAT15'],
            [
                'type' => 'fixed',
                'value' => 15.00,
                'min_order_amount' => 80.00,
                'usage_limit' => 500,
                'usage_limit_per_user' => 2,
                'is_active' => true,
            ]
        );

        // 2. Seed Shipping Methods
        ShippingMethod::firstOrCreate(
            ['code' => 'standard'],
            [
                'name' => 'Standard Ground Shipping',
                'cost' => 5.00,
                'free_shipping_threshold' => 100.00,
                'estimated_delivery_days' => '3-5 business days',
                'description' => 'Reliable and cost-effective ground delivery across the nation.',
                'is_active' => true,
            ]
        );

        ShippingMethod::firstOrCreate(
            ['code' => 'express'],
            [
                'name' => 'Express Priority Air',
                'cost' => 15.00,
                'free_shipping_threshold' => 250.00,
                'estimated_delivery_days' => '1-2 business days',
                'description' => 'Fast expedited delivery with priority handling.',
                'is_active' => true,
            ]
        );

        ShippingMethod::firstOrCreate(
            ['code' => 'pickup'],
            [
                'name' => 'Local Store Pickup',
                'cost' => 0.00,
                'estimated_delivery_days' => 'Ready in 2 hours',
                'description' => 'Pick up your order directly from our nearest hub.',
                'is_active' => true,
            ]
        );
    }
}
