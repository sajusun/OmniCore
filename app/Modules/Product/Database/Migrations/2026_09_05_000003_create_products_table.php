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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('product_brands')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->nullable()->unique();
            $table->string('type')->default('simple'); // simple, variable, digital
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('price', 12, 2)->default(0.00);
            $table->decimal('compare_at_price', 12, 2)->nullable();
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->boolean('manage_stock')->default(true);
            $table->integer('stock_quantity')->default(0);
            $table->integer('low_stock_threshold')->default(5);
            $table->boolean('is_in_stock')->default(true);
            $table->boolean('allow_backorders')->default(false);
            $table->string('status')->default('draft'); // draft, published, archived
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_refundable')->default(true);
            $table->decimal('weight', 8, 2)->nullable();
            $table->json('dimensions')->nullable(); // {length, width, height, unit}
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('sales_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
