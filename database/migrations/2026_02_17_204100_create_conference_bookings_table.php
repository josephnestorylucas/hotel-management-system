<?php
// database/migrations/2026_02_18_000002_create_conference_bookings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conference_bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('booking_number')->unique();
            $table->foreignUuid('conference_hall_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('guest_id')->constrained()->cascadeOnDelete();
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('total_cost', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            
            $table->index(['booking_date', 'conference_hall_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conference_bookings');
    }
};