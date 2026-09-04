<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Standard Shipping, Express Delivery, Overnight
            $table->string('code')->unique();
            $table->decimal('cost', 12, 2)->default(0.00);
            $table->decimal('free_shipping_threshold', 12, 2)->nullable(); // e.g. Free if order > $100
            $table->string('estimated_delivery_days')->nullable(); // e.g. "3-5 business days"
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};
