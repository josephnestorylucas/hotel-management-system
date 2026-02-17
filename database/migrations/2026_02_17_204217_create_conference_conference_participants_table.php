<?php
// database/migrations/2026_02_18_000004_create_conference_participants_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conference_participants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('conference_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->enum('role', ['speaker', 'attendee'])->default('attendee');
            $table->enum('rsvp_status', ['pending', 'confirmed', 'declined'])->default('pending');
            $table->string('access_token')->unique();
            $table->string('access_code', 8)->unique();
            $table->integer('checked_in_count')->default(0);
            $table->timestamp('last_check_in_at')->nullable();
            $table->timestamps();
            
            $table->index('access_token');
            $table->index('access_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conference_participants');
    }
};