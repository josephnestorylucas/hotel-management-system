<?php
// database/migrations/2026_02_27_000003_create_local_purchase_order_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('local_purchase_order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lpo_id');
            $table->uuid('product_id')->nullable();
            $table->string('item_name', 200);
            $table->string('unit', 50);
            $table->decimal('quantity', 10, 3);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('lpo_id')->references('id')->on('local_purchase_orders')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('local_purchase_order_items');
    }
};