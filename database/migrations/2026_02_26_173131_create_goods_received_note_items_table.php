<?php
// database/migrations/2026_02_27_000005_create_goods_received_note_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_received_note_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('grn_id');
            $table->uuid('lpo_item_id')->nullable();
            $table->uuid('product_id')->nullable();
            $table->string('item_name', 200);
            $table->string('unit', 50);
            $table->decimal('quantity_ordered', 10, 3);
            $table->decimal('quantity_received', 10, 3);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('grn_id')->references('id')->on('goods_received_notes')->cascadeOnDelete();
            $table->foreign('lpo_item_id')->references('id')->on('local_purchase_order_items')->nullOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_received_note_items');
    }
};