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
        Schema::create('walkin_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('transaction_number')->unique();
            
            // Module identification
            $table->string('module', 20); // laundry, restaurant, bar
            $table->uuid('order_id');
            $table->string('order_number', 50);
            
            // Customer information
            $table->string('customer_name', 150);
            $table->string('customer_phone', 30)->nullable();
            
            // Payment details
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('TZS');
            $table->string('payment_method', 20); // cash, card, mobile
            $table->string('provider_reference')->nullable(); // AzamPesa reference
            
            // Status tracking
            $table->string('status', 20)->default('pending'); // pending, completed, failed
            $table->json('metadata')->nullable();
            
            // Audit
            $table->uuid('created_by');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['module', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index('order_id');
            $table->index('customer_phone');
            
            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('walkin_transactions');
    }
};
