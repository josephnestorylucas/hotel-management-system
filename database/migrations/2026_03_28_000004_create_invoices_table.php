<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('invoice_no', 30)->unique();     // INV-20240222-0001
            $table->enum('invoice_type', [
                'guest_checkout',     // final hotel bill
                'restaurant',         // restaurant only
                'laundry',            // laundry only
            ]);
            $table->uuid('guest_id')->nullable();
            $table->string('guest_name', 150);
            $table->uuid('booking_id')->nullable();
            $table->date('invoice_date');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0); // 18% VAT
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'charge_to_booking'])->nullable();
            $table->enum('status', ['draft', 'issued', 'paid', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->uuid('issued_by');
            $table->timestamps();

            $table->foreign('issued_by')->references('id')->on('users');
            $table->index(['invoice_date', 'invoice_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
