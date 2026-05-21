<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_metrics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')->constrained()->cascadeOnDelete();
            $table->date('metric_date');
            $table->integer('total_registrations')->default(0);
            $table->integer('confirmed_registrations')->default(0);
            $table->integer('checked_in_count')->default(0);
            $table->integer('no_show_count')->default(0);
            $table->integer('cancellations')->default(0);
            $table->integer('avg_check_in_minutes')->nullable()->comment('Average check-in time in minutes from midnight');
            $table->integer('peak_check_in_hour')->nullable();
            $table->decimal('total_revenue_collected', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['event_id', 'metric_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_metrics');
    }
};
