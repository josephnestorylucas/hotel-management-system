<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            // order_type is already character varying(255) — no schema change needed.
            // The expanded enum values are validated at the application level.
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA writable_schema = 1');
            DB::statement("UPDATE sqlite_master SET sql = replace(sql, 'order_type IN (''guest'',''walkin'')', 'order_type IN (''guest'',''walkin'',''dine_in'',''room_service'',''bar_tab'',''takeaway'')') WHERE name = 'orders' AND type = 'table'");
            DB::statement('PRAGMA writable_schema = 0');
        } else {
            DB::statement("ALTER TABLE orders MODIFY COLUMN order_type ENUM('guest','walkin','dine_in','room_service','bar_tab','takeaway') NOT NULL");
        }
    }

    public function down(): void
    {
        // No down migration — existing columns remain compatible.
    }
};
