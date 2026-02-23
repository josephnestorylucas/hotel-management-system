<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->integer('loyalty_points')->default(0)->after('date_of_birth');
            $table->enum('loyalty_tier', ['none', 'Silver', 'Gold', 'Platinum'])->default('none')->after('loyalty_points');
            $table->timestamp('tier_upgraded_at')->nullable()->after('loyalty_tier');
            $table->integer('total_stays')->default(0)->after('tier_upgraded_at');
            $table->decimal('total_spent', 12, 2)->default(0)->after('total_stays');
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn(['loyalty_points', 'loyalty_tier', 'tier_upgraded_at', 'total_stays', 'total_spent']);
        });
    }
};
