<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conferences', function (Blueprint $table) {
            $table->decimal('conference_fee', 12, 2)->default(0)->after('description');
            $table->string('hall_name')->nullable()->after('conference_fee');
            $table->string('venue_notes')->nullable()->after('hall_name');
            $table->foreignUuid('conference_booking_id')->nullable()->change();
            $table->foreignUuid('guest_id')->nullable()->change();
        });

        Schema::table('conference_participants', function (Blueprint $table) {
            $table->integer('pass_number')->nullable()->after('access_code');
            $table->string('pass_type', 20)->default('attendee')->after('pass_number');
            $table->index(['conference_id', 'pass_number']);
        });

        DB::statement("ALTER TABLE conference_participants DROP CONSTRAINT IF EXISTS conference_participants_role_check");
        DB::statement("ALTER TABLE conference_participants ADD CONSTRAINT conference_participants_role_check CHECK (role IN ('speaker', 'attendee', 'organizer'))");
    }

    public function down(): void
    {
        Schema::table('conferences', function (Blueprint $table) {
            $table->dropColumn(['conference_fee', 'hall_name', 'venue_notes']);
        });

        Schema::table('conference_participants', function (Blueprint $table) {
            $table->dropColumn(['pass_number', 'pass_type']);
            $table->dropIndex(['conference_id', 'pass_number']);
        });
    }
};
