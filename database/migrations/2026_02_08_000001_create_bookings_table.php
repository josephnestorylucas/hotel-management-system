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
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('booking_number')->unique();

            // Guest association
            $table->uuid('guest_id')->nullable();
            $table->foreign('guest_id')->references('id')->on('guests')->nullOnDelete();

            // Legacy guest fields (for public bookings without a guest account)
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone');
            $table->string('guest_country')->nullable();

            // Room assignment
            $table->uuid('room_id');
            $table->foreign('room_id')->references('id')->on('rooms')->restrictOnDelete();

            // Linked reservation (auto-created from booking)
            $table->uuid('reservation_id')->nullable();
            $table->foreign('reservation_id')->references('id')->on('reservations')->nullOnDelete();

            // Booking details
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->integer('number_of_guests')->default(1);
            $table->decimal('total_amount', 10, 2);
            $table->text('special_requests')->nullable();

            // Status management
            $table->enum('status', [
                'pending',       // Just booked, awaiting confirmation
                'confirmed',     // Confirmed by staff
                'checked_in',    // Guest arrived
                'checked_out',   // Guest departed
                'cancelled',     // Cancelled
                'no_show',       // Guest didn't show up
            ])->default('pending');

            // Booking source tracking
            $table->enum('source', [
                'online',        // Public website booking
                'frontdesk',     // Created by front desk staff
                'phone',         // Phone reservation
                'walkin',        // Walk-in guest
            ])->default('online');

            // Staff tracking (nullable for public bookings)
            $table->uuid('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();

            $table->text('cancellation_reason')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('check_in_date');
            $table->index('check_out_date');
            $table->index('source');
            $table->index(['check_in_date', 'check_out_date', 'status']);
        });

        // Add booking_id to reservations for reverse lookup
        Schema::table('reservations', function (Blueprint $table) {
            $table->uuid('booking_id')->nullable()->after('guest_id');
            $table->foreign('booking_id')->references('id')->on('bookings')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropColumn('booking_id');
        });

        Schema::dropIfExists('bookings');
    }
};
