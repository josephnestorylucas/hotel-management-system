<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')->constrained()->cascadeOnDelete();
            $table->integer('session_number');
            $table->string('name');
            $table->text('description')->nullable();
            $table->dateTime('start_datetime')->index();
            $table->dateTime('end_datetime');
            $table->string('location')->nullable();
            $table->string('speaker_name')->nullable();
            $table->string('speaker_email')->nullable();
            $table->enum('session_type', ['keynote', 'workshop', 'networking', 'break', 'panel', 'presentation', 'other'])->default('presentation');
            $table->integer('max_capacity')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'session_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_schedules');
    }
};
