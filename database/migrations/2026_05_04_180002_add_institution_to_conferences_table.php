<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conferences', function (Blueprint $table) {
            $table->foreignUuid('institution_id')->nullable()->after('conference_booking_id')->constrained()->nullOnDelete();
            $table->foreignUuid('guest_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('conferences', function (Blueprint $table) {
            $table->dropForeign(['institution_id']);
            $table->dropColumn('institution_id');
            $table->foreignUuid('guest_id')->nullable(false)->change();
        });
    }
};
