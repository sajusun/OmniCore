<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('club_id')->nullable()->constrained()->nullOnDelete();

            // Basic
            $table->string('title');
            $table->text('description')->nullable();

            // Enum
            $table->string('event_type');

            // Location
            $table->string('location');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Date Time
            $table->date('event_date');
            $table->time('event_time');

            // Capacity
            $table->unsignedInteger('max_participants')->nullable();
            $table->json('vehicles_required')->nullable();

            // Visibility
            $table->boolean('is_public')->default(true);

            // Status
            $table->enum('status', [
                'draft',
                'published',
                'cancelled',
                'completed',
            ])->default('published');

            $table->timestamps();
            $table->softDeletes();

            $table->index('event_date');
            $table->index('event_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
