<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('floor_id');
            $table->foreign('floor_id')->references('id')->on('floors')->restrictOnDelete();
            $table->uuid('room_type_id');
            $table->foreign('room_type_id')->references('id')->on('room_types')->restrictOnDelete();
            $table->string('room_number');
            $table->enum('status', ['available', 'reserved', 'occupied', 'dirty', 'out_of_order'])->default('available');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['floor_id', 'room_number']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('rooms');
    }
};