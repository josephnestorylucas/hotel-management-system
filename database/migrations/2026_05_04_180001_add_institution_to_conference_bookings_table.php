<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conference_bookings', function (Blueprint $table) {
            $table->foreignUuid('institution_id')->nullable()->after('conference_hall_id')->constrained()->nullOnDelete();
            $table->foreignUuid('guest_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('conference_bookings', function (Blueprint $table) {
            $table->dropForeign(['institution_id']);
            $table->dropColumn('institution_id');
            $table->foreignUuid('guest_id')->nullable(false)->change();
        });
    }
};
