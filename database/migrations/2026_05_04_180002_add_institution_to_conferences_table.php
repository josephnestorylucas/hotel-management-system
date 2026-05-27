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

        Schema::table('conferences', function (Blueprint $table) {
            if (!Schema::hasColumn('conferences', 'institution_id')) {
                $table->foreignUuid('institution_id')->nullable()->after('conference_booking_id')->constrained()->nullOnDelete();
            }
        });

        if ($driver !== 'sqlite') {
            Schema::table('conferences', function (Blueprint $table) {
                $table->foreignUuid('guest_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        Schema::table('conferences', function (Blueprint $table) {
            if (Schema::hasColumn('conferences', 'institution_id')) {
                $table->dropForeign(['institution_id']);
                $table->dropColumn('institution_id');
            }
        });

        if ($driver !== 'sqlite') {
            Schema::table('conferences', function (Blueprint $table) {
                $table->foreignUuid('guest_id')->nullable(false)->change();
            });
        }
    }
};
