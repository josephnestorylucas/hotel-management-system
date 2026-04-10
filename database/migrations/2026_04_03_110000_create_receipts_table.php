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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('receipt_number')->unique();
            
            // Module identification
            $table->string('module'); // laundry, restaurant, bar, checkout, walkin, conference
            
            // Polymorphic relation to the source record
            $table->nullableMorphs('receiptable');
            
            // Customer information (snapshot at time of receipt)
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            
            // Items snapshot (JSON array of line items)
            $table->json('items_snapshot')->nullable();
            
            // Financial details
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);
            $table->string('currency', 3)->default('TZS');
            
            // Payment information
            $table->string('payment_method')->nullable(); // cash, card, mobile_money, bank_transfer
            $table->string('payment_status')->default('unpaid'); // paid, partial, unpaid, refunded
            $table->string('transaction_reference')->nullable();
            
            // Staff who issued the receipt
            $table->foreignUuid('cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('cashier_name')->nullable(); // Snapshot in case user is deleted
            
            // Additional info
            $table->text('notes')->nullable();
            
            // Refund tracking
            $table->boolean('is_refund')->default(false);
            $table->foreignId('refund_receipt_id')->nullable()->constrained('receipts')->nullOnDelete();
            
            // Timestamps
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('printed_at')->nullable();
            $table->unsignedInteger('print_count')->default(0);
            
            $table->timestamps();
            
            // Indexes for common queries
            $table->index('module');
            $table->index('payment_status');
            $table->index('issued_at');
            $table->index(['receiptable_type', 'receiptable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
