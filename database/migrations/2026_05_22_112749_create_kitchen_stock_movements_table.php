<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kitchen_stock_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kitchen_stock_item_id')->constrained('kitchen_stock_items')->cascadeOnDelete();
            $table->enum('movement_type', ['purchase', 'damage', 'transfer', 'adjustment']);
            $table->decimal('quantity', 10, 2);
            $table->text('notes')->nullable();
            $table->foreignUuid('recorded_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kitchen_stock_movements');
    }
};
