<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->foreignUuid('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('total_events_attended')->default(0);
            $table->json('event_preferences')->nullable();
            $table->json('communication_preferences')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn(['organization_id', 'total_events_attended', 'event_preferences', 'communication_preferences']);
        });
    }
};
