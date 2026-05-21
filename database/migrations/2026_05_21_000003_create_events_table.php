<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('conference_type_id')->nullable()->constrained('conference_types')->nullOnDelete();
            $table->string('title')->index();
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('theme_color', 7)->nullable();
            $table->enum('status', ['draft', 'scheduled', 'ongoing', 'completed', 'cancelled'])->default('draft');
            $table->enum('visibility', ['public', 'private', 'organization-only'])->default('public');
            $table->integer('capacity')->nullable();
            $table->foreignUuid('organizer_id')->nullable()->constrained('guests')->nullOnDelete();
            $table->integer('expected_attendance')->nullable();
            $table->integer('actual_attendance')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'slug']);
            $table->index(['status', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
