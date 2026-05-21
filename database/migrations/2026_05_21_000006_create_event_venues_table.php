<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_venues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('conference_hall_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('booking_id')->nullable()->constrained('conference_bookings')->nullOnDelete();
            $table->enum('setup_type', ['theater', 'classroom', 'banquet', 'boardroom', 'hollow_square', 'cocktail'])->default('theater');
            $table->text('room_layout_notes')->nullable();
            $table->text('special_requests')->nullable();
            $table->dateTime('expected_setup_start')->nullable();
            $table->dateTime('expected_setup_end')->nullable();
            $table->dateTime('teardown_start')->nullable();
            $table->dateTime('teardown_end')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'setup_in_progress', 'active', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_venues');
    }
};
