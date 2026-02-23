<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('discount_audits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('booking_id');
            $table->uuid('authorized_by');
            $table->decimal('discount_amount', 10, 2);
            $table->integer('valid_days');
            $table->date('valid_from');
            $table->date('valid_until');
            $table->text('reason')->nullable();
            $table->timestamp('authorized_at');
            $table->timestamps();

            $table->foreign('authorized_by')->references('id')->on('users');
            $table->index(['booking_id', 'authorized_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_audits');
    }
};
