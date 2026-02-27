<?php
// database/migrations/2026_02_27_000002_create_local_purchase_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('local_purchase_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('lpo_number', 30)->unique();
            $table->uuid('supplier_id')->nullable();
            $table->string('supplier_name_manual', 200)->nullable();
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->enum('status', [
                'draft',
                'pending_approval',
                'approved',
                'rejected',
                'sent',
                'partially_received',
                'fully_received',
                'cancelled',
            ])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->uuid('created_by');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            
            $table->index('status');
            $table->index('order_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('local_purchase_orders');
    }
};