<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // In your 2026_02_17_100000_refactor_reservation_booking_architecture.php migration

    public function up(): void
    {
        // First, change the ENUM to include 'converted' and remove old statuses
        DB::statement("ALTER TABLE reservations DROP CONSTRAINT IF EXISTS reservations_status_check");
        
        // For PostgreSQL, you need to recreate the ENUM type or use a check constraint
        DB::statement("ALTER TABLE reservations ALTER COLUMN status TYPE text");
        DB::statement("ALTER TABLE reservations ADD CONSTRAINT reservations_status_check CHECK (status IN ('pending', 'confirmed', 'cancelled', 'no_show', 'converted'))");
        
        // Now you can safely update
        DB::table('reservations')
            ->whereIn('status', ['checked_in', 'checked_out'])
            ->update(['status' => 'converted']);
        
        // Then handle data migration to the new structure...
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
