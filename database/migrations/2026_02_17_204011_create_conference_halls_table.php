<?php
// database/migrations/2026_02_18_000001_create_conference_halls_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conference_halls', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('location');
            $table->integer('capacity');
            $table->decimal('hourly_rate', 10, 2);
            $table->enum('status', ['available', 'maintenance'])->default('available');
            $table->json('amenities')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conference_halls');
    }
};