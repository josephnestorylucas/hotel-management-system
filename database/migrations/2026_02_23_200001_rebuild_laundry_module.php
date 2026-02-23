<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;

return new class extends Migration {
    public function up(): void
    {
        // ── Drop old laundry tables ──────────────────────────────────────────
        Schema::dropIfExists('laundry_order_items');
        Schema::dropIfExists('laundry_orders');
        Schema::dropIfExists('laundry_items');

        // ── Add LAUNDRY_MANAGER role ─────────────────────────────────────────
        Role::updateOrCreate(
            ['name' => 'laundry_manager'],
            ['description' => 'Manages laundry pricing, reports, and full order oversight']
        );

        // ── laundry_services ─────────────────────────────────────────────────
        Schema::create('laundry_services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->integer('turnaround_hours')->default(24);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── laundry_service_items ────────────────────────────────────────────
        Schema::create('laundry_service_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('laundry_service_id');
            $table->string('item_name', 100);
            $table->decimal('price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('laundry_service_id')
                  ->references('id')
                  ->on('laundry_services')
                  ->cascadeOnDelete();

            $table->unique(['laundry_service_id', 'item_name']);
        });

        // ── laundry_orders ───────────────────────────────────────────────────
        Schema::create('laundry_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('order_number', 30)->unique();

            $table->enum('customer_type', ['guest', 'walkin']);

            // Guest fields
            $table->uuid('booking_id')->nullable();
            $table->string('room_number', 20)->nullable();

            // Walk-in fields
            $table->string('customer_name', 150)->nullable();
            $table->string('customer_phone', 30)->nullable();

            $table->enum('status', [
                'received',
                'processing',
                'ready',
                'delivered',
                'collected',
                'settled',
                'cancelled',
            ])->default('received');

            $table->text('special_instructions')->nullable();

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->enum('payment_method', [
                'cash',
                'card',
                'charge_to_booking',
            ])->nullable();

            $table->timestamp('expected_ready_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('collected_at')->nullable();
            $table->timestamp('settled_at')->nullable();

            $table->uuid('received_by');
            $table->uuid('processed_by')->nullable();
            $table->uuid('delivered_by')->nullable();
            $table->uuid('settled_by')->nullable();

            $table->timestamps();

            $table->foreign('received_by')->references('id')->on('users');
        });

        // ── laundry_order_items ──────────────────────────────────────────────
        Schema::create('laundry_order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('laundry_order_id');
            $table->uuid('laundry_service_item_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('laundry_order_id')
                  ->references('id')
                  ->on('laundry_orders')
                  ->cascadeOnDelete();

            $table->foreign('laundry_service_item_id')
                  ->references('id')
                  ->on('laundry_service_items');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_order_items');
        Schema::dropIfExists('laundry_orders');
        Schema::dropIfExists('laundry_service_items');
        Schema::dropIfExists('laundry_services');
    }
};
