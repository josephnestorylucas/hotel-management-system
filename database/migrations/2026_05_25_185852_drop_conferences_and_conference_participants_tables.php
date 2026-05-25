<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('conference_participants');
        Schema::dropIfExists('conferences');
    }

    public function down(): void
    {
        Schema::create('conferences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('conference_booking_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('draft');
            $table->decimal('conference_fee', 10, 2)->default(0);
            $table->uuid('institution_id')->nullable();
            $table->uuid('guest_id')->nullable();
            $table->string('pass_code')->nullable();
            $table->timestamps();

            $table->foreign('conference_booking_id')->references('id')->on('conference_bookings')->onDelete('cascade');
            $table->foreign('institution_id')->references('id')->on('institutions')->onDelete('set null');
            $table->foreign('guest_id')->references('id')->on('guests')->onDelete('set null');
        });

        Schema::create('conference_participants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('conference_id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('organization')->nullable();
            $table->string('role')->nullable();
            $table->string('badge_number')->unique()->nullable();
            $table->string('pass_code')->unique()->nullable();
            $table->string('check_in_status')->default('pending');
            $table->timestamp('checked_in_at')->nullable();
            $table->uuid('guest_id')->nullable();
            $table->timestamps();

            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
            $table->foreign('guest_id')->references('id')->on('guests')->onDelete('set null');
        });
    }
};
