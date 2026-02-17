<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Step 1: Convert existing checked_in/checked_out reservations to 'converted' ───
        DB::table('reservations')
            ->whereIn('status', ['checked_in', 'checked_out'])
            ->update(['status' => 'pending']); // Temporary — will become 'converted' after enum change

        // ─── Step 2: Alter reservation status enum — add 'converted', remove 'checked_in'/'checked_out' ───
        DB::statement("ALTER TABLE reservations MODIFY COLUMN status ENUM('pending', 'confirmed', 'cancelled', 'no_show', 'converted') DEFAULT 'pending'");

        // Mark previously converted rows
        // (They were set to 'pending' temporarily above; if they had a booking_id, mark as converted)
        DB::table('reservations')
            ->whereNotNull('booking_id')
            ->update(['status' => 'converted']);

        // ─── Step 3: Rename total_amount → estimated_amount on reservations ───
        Schema::table('reservations', function (Blueprint $table) {
            $table->renameColumn('total_amount', 'estimated_amount');
        });

        // ─── Step 4: Make created_by nullable on reservations (for public/online reservations) ───
        Schema::table('reservations', function (Blueprint $table) {
            $table->uuid('created_by')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Revert created_by to required
        Schema::table('reservations', function (Blueprint $table) {
            $table->uuid('created_by')->nullable(false)->change();
        });

        // Rename estimated_amount back to total_amount
        Schema::table('reservations', function (Blueprint $table) {
            $table->renameColumn('estimated_amount', 'total_amount');
        });

        // Restore original enum (with checked_in/checked_out)
        DB::statement("ALTER TABLE reservations MODIFY COLUMN status ENUM('pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show') DEFAULT 'pending'");
    }
};
