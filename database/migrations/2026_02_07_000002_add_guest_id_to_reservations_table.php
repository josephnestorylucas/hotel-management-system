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
        Schema::table('reservations', function (Blueprint $table) {
            // Add guest_id column
            $table->uuid('guest_id')->nullable()->after('room_id');
            $table->foreign('guest_id')->references('id')->on('guests')->nullOnDelete();

            // Keep legacy columns for backward compatibility but make them nullable
            // These will be used for reservations that don't have a guest record
            $table->string('guest_name')->nullable()->change();
            $table->string('guest_phone')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['guest_id']);
            $table->dropColumn('guest_id');
            
            // Restore original constraints
            $table->string('guest_name')->nullable(false)->change();
            $table->string('guest_phone')->nullable(false)->change();
        });
    }
};
