<?php
// database/migrations/2026_02_27_000004_create_goods_received_notes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_received_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('grn_number', 30)->unique();
            $table->uuid('lpo_id');
            $table->uuid('supplier_id')->nullable();
            $table->string('supplier_name_manual', 200)->nullable();
            $table->date('received_date');
            $table->string('delivery_vehicle', 100)->nullable();
            $table->string('driver_name', 100)->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('receipt_path', 500)->nullable();
            $table->enum('status', [
                'draft',
                'pending_confirmation',
                'confirmed',
                'rejected',
            ])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->uuid('received_by');
            $table->uuid('confirmed_by')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->foreign('lpo_id')->references('id')->on('local_purchase_orders')->cascadeOnDelete();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();
            $table->foreign('received_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('confirmed_by')->references('id')->on('users')->nullOnDelete();
            
            $table->index('status');
            $table->index('received_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_received_notes');
    }
};