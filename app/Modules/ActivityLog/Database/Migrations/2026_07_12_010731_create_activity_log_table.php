<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('event');
                $table->string('module');
                $table->nullableMorphs('subject');
                $table->text('description')->nullable();
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->json('properties')->nullable();
                $table->ipAddress('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->string('url')->nullable();
                $table->string('method', 10)->nullable();
                $table->uuid('batch_uuid')->nullable()->index();

                $table->timestamps();

                $table->index('event');
                $table->index('module');
                $table->index('created_at');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
};
