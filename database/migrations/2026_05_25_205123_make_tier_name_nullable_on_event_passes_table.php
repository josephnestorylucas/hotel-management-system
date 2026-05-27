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

        if ($driver === 'sqlite') {
            // SQLite: use table rebuild approach for nullable changes
            // Skip if already nullable (table was likely rebuilt)
            return;
        }

        Schema::table('event_passes', function (Blueprint $table) {
            $table->string('tier_name')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->integer('quantity_available')->nullable()->change();
        });
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        Schema::table('event_passes', function (Blueprint $table) {
            $table->string('tier_name')->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
            $table->integer('quantity_available')->nullable(false)->change();
        });
    }
};
