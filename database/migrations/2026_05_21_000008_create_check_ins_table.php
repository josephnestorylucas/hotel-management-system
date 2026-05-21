<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('check_ins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('attendance_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('event_schedule_id')->nullable()->constrained('event_schedules')->nullOnDelete();
            $table->enum('check_in_type', ['entry', 'exit', 'manual_override'])->default('entry');
            $table->enum('check_in_method', ['qr_scan', 'manual_code', 'staff_override'])->default('qr_scan');
            $table->foreignUuid('staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address')->nullable();
            $table->string('device_info')->nullable();
            $table->string('location')->nullable();
            $table->text('verification_notes')->nullable();
            $table->timestamps();

            $table->index(['attendance_id', 'created_at']);
            $table->index('event_schedule_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_ins');
    }
};
