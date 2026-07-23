<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('club_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Role: admin can manage members, moderate content; member is a regular participant
            $table->enum('role', ['admin', 'member'])->default('member');

            /**
             * Status field — future-ready for admin approval workflow.
             *
             * Current behaviour  : auto-approved on join.
             * Future behaviour   : club can set 'approval_required = true' and new
             *                      join requests arrive as 'pending' until an admin
             *                      approves or rejects them.
             */
            $table->enum('status', ['approved', 'pending', 'rejected'])->default('approved');

            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('approved_at')->nullable();

            // Track which admin approved/rejected this membership (future use)
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // One user can only be a member of a club once
            $table->unique(['club_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_members');
    }
};
