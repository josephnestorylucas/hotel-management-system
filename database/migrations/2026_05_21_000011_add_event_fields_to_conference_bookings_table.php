<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conference_bookings', function (Blueprint $table) {
            $table->foreignUuid('event_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('organization_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('conference_bookings', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropForeign(['organization_id']);
            $table->dropColumn(['event_id', 'organization_id']);
        });
    }
};
