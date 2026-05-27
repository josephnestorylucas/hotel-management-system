<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            // SQLite doesn't support DROP CONSTRAINT
            // CHECK constraints are enforced at application level
            return;
        }

        DB::statement('ALTER TABLE event_passes DROP CONSTRAINT IF EXISTS event_tickets_access_type_check');
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE event_passes ADD CONSTRAINT event_tickets_access_type_check CHECK (access_type IN ('single-session','all-sessions','day-pass','unlimited'))");
    }
};
