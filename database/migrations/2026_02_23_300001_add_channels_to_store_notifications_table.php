<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('store_notifications', function (Blueprint $table) {
            $table->boolean('is_emailed')->default(false)->after('is_read');
            $table->boolean('is_sms_sent')->default(false)->after('is_emailed');
        });
    }

    public function down(): void
    {
        Schema::table('store_notifications', function (Blueprint $table) {
            $table->dropColumn(['is_emailed', 'is_sms_sent']);
        });
    }
};
