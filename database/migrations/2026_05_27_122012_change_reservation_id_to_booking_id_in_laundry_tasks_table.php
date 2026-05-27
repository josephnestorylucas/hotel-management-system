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
        Schema::table('laundry_tasks', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['reservation_id']);
            // Drop the column
            $table->dropColumn('reservation_id');
        });

        Schema::table('laundry_tasks', function (Blueprint $table) {
            // Add booking_id column with foreign key
            $table->foreignUuid('booking_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laundry_tasks', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['booking_id']);
            // Drop the column
            $table->dropColumn('booking_id');
        });

        Schema::table('laundry_tasks', function (Blueprint $table) {
            // Add reservation_id column back
            $table->foreignUuid('reservation_id')->constrained()->cascadeOnDelete();
        });
    }
};
