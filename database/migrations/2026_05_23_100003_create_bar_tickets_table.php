<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bar_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->uuid('table_id')->nullable();
            $table->json('items');
            $table->enum('status', ['pending', 'preparing', 'ready'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('printed_at')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreign('table_id')->references('id')->on('tables')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bar_tickets');
    }
};
