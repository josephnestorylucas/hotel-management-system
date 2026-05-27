<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('bugs')->create('bug_reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('details');
            $table->string('page_url')->nullable();
            $table->string('module');
            $table->string('severity');
            $table->string('reported_by')->nullable();
            $table->string('status')->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('bugs')->dropIfExists('bug_reports');
    }
};
