<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        // ─── Step 1: Convert existing checked_in/checked_out reservations to 'converted' ───
        DB::table('reservations')
            ->whereIn('status', ['checked_in', 'checked_out'])
            ->update(['status' => 'converted']);

        // Mark reservations that have a booking_id as converted
        DB::table('reservations')
            ->whereNotNull('booking_id')
            ->update(['status' => 'converted']);

        // ─── Step 2: Rename total_amount → estimated_amount on reservations ───
        if (Schema::hasColumn('reservations', 'total_amount')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->renameColumn('total_amount', 'estimated_amount');
            });
        }

        // ─── Step 3: Make created_by nullable on reservations ───
        if ($driver !== 'sqlite') {
            Schema::table('reservations', function (Blueprint $table) {
                $table->uuid('created_by')->nullable()->change();
            });
        }
        // SQLite: columns are nullable by default unless NOT NULL is specified at creation
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver !== 'sqlite') {
            Schema::table('reservations', function (Blueprint $table) {
                $table->uuid('created_by')->nullable(false)->change();
            });
        }

        if (Schema::hasColumn('reservations', 'estimated_amount')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->renameColumn('estimated_amount', 'total_amount');
            });
        }

        DB::table('reservations')
            ->where('status', 'converted')
            ->update(['status' => 'pending']);
    }
};
