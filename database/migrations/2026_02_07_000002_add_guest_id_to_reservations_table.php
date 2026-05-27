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

        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'guest_id')) {
                $table->uuid('guest_id')->nullable()->after('room_id');
                $table->foreign('guest_id')->references('id')->on('guests')->nullOnDelete();
            }
        });

        if ($driver !== 'sqlite') {
            Schema::table('reservations', function (Blueprint $table) {
                $table->string('guest_name')->nullable()->change();
                $table->string('guest_phone')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        Schema::table('reservations', function (Blueprint $table) {
            if (Schema::hasColumn('reservations', 'guest_id')) {
                $table->dropForeign(['guest_id']);
                $table->dropColumn('guest_id');
            }
        });

        if ($driver !== 'sqlite') {
            Schema::table('reservations', function (Blueprint $table) {
                $table->string('guest_name')->nullable(false)->change();
                $table->string('guest_phone')->nullable(false)->change();
            });
        }
    }
};
