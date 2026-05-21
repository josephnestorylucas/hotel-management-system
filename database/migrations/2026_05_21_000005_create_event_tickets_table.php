<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')->constrained()->cascadeOnDelete();
            $table->string('tier_name');
            $table->enum('tier_type', ['standard', 'vip', 'exhibitor', 'speaker', 'student', 'corporate', 'media', 'press'])->default('standard');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('quantity_available')->nullable();
            $table->integer('quantity_sold')->default(0);
            $table->date('early_bird_until')->nullable();
            $table->json('benefits')->nullable();
            $table->boolean('includes_guide')->default(false);
            $table->enum('access_type', ['single-session', 'all-sessions', 'day-pass', 'unlimited'])->default('all-sessions');
            $table->decimal('bulk_discount_percent', 5, 2)->nullable();
            $table->date('sale_start_date')->nullable();
            $table->date('sale_end_date')->nullable();
            $table->enum('status', ['draft', 'on_sale', 'sold_out', 'archived'])->default('draft');
            $table->string('color', 7)->nullable();
            $table->timestamps();

            $table->index(['event_id', 'status']);
            $table->unique(['event_id', 'tier_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_tickets');
    }
};
