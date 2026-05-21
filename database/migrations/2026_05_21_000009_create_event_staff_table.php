<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_staff', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['organizer', 'co-organizer', 'speaker', 'moderator', 'panelist', 'mentor', 'judge', 'coordinator', 'check_in_staff', 'admin'])->default('coordinator');
            $table->text('display_bio')->nullable();
            $table->string('display_photo')->nullable();
            $table->json('session_ids')->nullable();
            $table->json('permissions')->nullable();
            $table->dateTime('assigned_at')->default(now());
            $table->dateTime('removed_at')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
            $table->index(['event_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_staff');
    }
};
