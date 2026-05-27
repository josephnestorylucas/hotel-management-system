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

        Schema::table('conference_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('conference_bookings', 'institution_id')) {
                $table->foreignUuid('institution_id')->nullable()->after('conference_hall_id')->constrained()->nullOnDelete();
            }
        });

        if ($driver !== 'sqlite') {
            Schema::table('conference_bookings', function (Blueprint $table) {
                $table->foreignUuid('guest_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        Schema::table('conference_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('conference_bookings', 'institution_id')) {
                $table->dropForeign(['institution_id']);
                $table->dropColumn('institution_id');
            }
        });

        if ($driver !== 'sqlite') {
            Schema::table('conference_bookings', function (Blueprint $table) {
                $table->foreignUuid('guest_id')->nullable(false)->change();
            });
        }
    }
};
