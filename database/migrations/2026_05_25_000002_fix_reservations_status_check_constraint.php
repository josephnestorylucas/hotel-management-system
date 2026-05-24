<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the old check constraint and add new one with 'converted' included
        DB::statement('ALTER TABLE reservations DROP CONSTRAINT IF EXISTS reservations_status_check');
        DB::statement("ALTER TABLE reservations ADD CONSTRAINT reservations_status_check CHECK (status IN ('pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show', 'converted'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE reservations DROP CONSTRAINT IF EXISTS reservations_status_check');
        DB::statement("ALTER TABLE reservations ADD CONSTRAINT reservations_status_check CHECK (status IN ('pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show'))");
    }
};
