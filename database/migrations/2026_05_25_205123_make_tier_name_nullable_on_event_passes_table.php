<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_passes', function (Blueprint $table) {
            $table->string('tier_name')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->integer('quantity_available')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('event_passes', function (Blueprint $table) {
            $table->string('tier_name')->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
            $table->integer('quantity_available')->nullable(false)->change();
        });
    }
};
