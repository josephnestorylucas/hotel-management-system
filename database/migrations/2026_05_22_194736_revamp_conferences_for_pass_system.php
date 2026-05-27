<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // conferences table - add columns only if they don't exist
        Schema::table('conferences', function (Blueprint $table) {
            if (!Schema::hasColumn('conferences', 'conference_fee')) {
                $table->decimal('conference_fee', 12, 2)->default(0)->after('description');
            }
            if (!Schema::hasColumn('conferences', 'hall_name')) {
                $table->string('hall_name')->nullable()->after('conference_fee');
            }
            if (!Schema::hasColumn('conferences', 'venue_notes')) {
                $table->string('venue_notes')->nullable()->after('hall_name');
            }
        });

        // conference_participants table - add columns only if they don't exist
        Schema::table('conference_participants', function (Blueprint $table) {
            if (!Schema::hasColumn('conference_participants', 'pass_number')) {
                $table->integer('pass_number')->nullable()->after('access_code');
            }
            if (!Schema::hasColumn('conference_participants', 'pass_type')) {
                $table->string('pass_type', 20)->default('attendee')->after('pass_number');
            }
        });

        // Skip CHECK constraint modification - SQLite doesn't support DROP CONSTRAINT
        // The role column values are enforced at application level
    }

    public function down(): void
    {
        Schema::table('conferences', function (Blueprint $table) {
            $table->dropColumn(['conference_fee', 'hall_name', 'venue_notes']);
        });

        Schema::table('conference_participants', function (Blueprint $table) {
            $table->dropColumn(['pass_number', 'pass_type']);
        });
    }
};
