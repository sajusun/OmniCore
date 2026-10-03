<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Optimize Products Table Indexes
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $table->index(['status', 'is_featured', 'created_at'], 'idx_products_featured_feed');
                $table->index(['status', 'category_id', 'price'], 'idx_products_category_price');
                $table->index(['status', 'brand_id'], 'idx_products_brand');
                $table->index(['status', 'sales_count'], 'idx_products_best_selling');
            });
        }

        // 2. Optimize Orders Table Indexes
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->index(['user_id', 'status', 'created_at'], 'idx_orders_user_history');
                $table->index(['status', 'created_at'], 'idx_orders_status_date');
                $table->index(['payment_status', 'created_at'], 'idx_orders_payment_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropIndex('idx_products_featured_feed');
                $table->dropIndex('idx_products_category_price');
                $table->dropIndex('idx_products_brand');
                $table->dropIndex('idx_products_best_selling');
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex('idx_orders_user_history');
                $table->dropIndex('idx_orders_status_date');
                $table->dropIndex('idx_orders_payment_date');
            });
        }
    }
};
