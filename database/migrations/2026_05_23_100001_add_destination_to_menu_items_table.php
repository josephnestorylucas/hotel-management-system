<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('destination', 20)->default('kitchen')->after('service_location_tag');
            $table->boolean('is_buffet')->default(false)->after('destination');
            $table->time('available_from')->nullable()->after('is_buffet');
            $table->time('available_until')->nullable()->after('available_from');
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn(['destination', 'is_buffet', 'available_from', 'available_until']);
        });
    }
};
