<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE event_passes DROP CONSTRAINT IF EXISTS event_tickets_access_type_check');
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE event_passes ADD CONSTRAINT event_tickets_access_type_check CHECK (access_type IN ('single-session','all-sessions','day-pass','unlimited'))");
    }
};
