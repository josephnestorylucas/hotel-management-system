<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('event_ticket_id')->nullable()->constrained('event_tickets')->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->index();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('job_title')->nullable();
            $table->foreignUuid('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('registration_type', ['individual', 'bulk_registered', 'walked_in', 'complimentary'])->default('individual');
            $table->string('ticket_number')->unique();
            $table->string('qr_token', 64)->unique();
            $table->string('manual_code', 8)->unique();
            $table->dateTime('registration_date')->useCurrent();
            $table->enum('registration_status', ['pending', 'confirmed', 'cancelled', 'no_show'])->default('pending');
            $table->string('dietary_requirements')->nullable();
            $table->string('special_accommodations')->nullable();
            $table->text('notes')->nullable();
            $table->json('guest_metadata')->nullable();
            $table->timestamp('first_check_in_at')->nullable();
            $table->timestamp('last_check_in_at')->nullable();
            $table->integer('total_check_ins')->default(0);
            $table->timestamp('badge_printed_at')->nullable();
            $table->boolean('data_shared_consent')->default(false);
            $table->timestamps();

            $table->index(['event_id', 'registration_status']);
            $table->index(['qr_token', 'manual_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
