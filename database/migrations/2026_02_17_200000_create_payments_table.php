<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('payment_number')->unique();      // PAY-xxxxx

            // ─── Linked entities ───
            $table->uuid('booking_id');
            $table->string('charge_type')->default('booking'); // booking, laundry, conference, service
            $table->uuid('reference_id')->nullable();          // FK to booking_charges.id (optional)

            // ─── Provider details ───
            $table->string('provider_name');                   // azampesa, stripe, paypal
            $table->string('provider_reference')->nullable();  // provider transaction id
            $table->string('payment_method')->nullable();      // mobile, card, dynamic-qr, bank_transfer

            // ─── Amount ───
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('TZS');

            // ─── Status ───
            $table->string('status')->default('pending');      // pending, successful, failed, refunded, expired

            // ─── Dates ───
            $table->timestamp('payment_date')->nullable();     // when payment was confirmed
            $table->timestamp('refunded_at')->nullable();

            // ─── Metadata ───
            $table->json('metadata')->nullable();              // full provider response for audit
            $table->json('refund_metadata')->nullable();       // refund response data

            // ─── URLs (for card/QR redirect flows) ───
            $table->string('payment_url')->nullable();         // external checkout URL
            $table->text('payment_qr_code')->nullable();       // QR code data string
            $table->string('payment_token')->nullable();       // provider token

            // ─── User who initiated ───
            $table->uuid('created_by')->nullable();

            // ─── Idempotency ───
            $table->string('idempotency_key')->unique()->nullable();

            $table->timestamps();

            // ─── Foreign keys ───
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('reference_id')->references('id')->on('booking_charges')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            // ─── Indexes ───
            $table->index('provider_name');
            $table->index('status');
            $table->index('booking_id');
            $table->index('provider_reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
