<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petty_cash_expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference_no', 30)->unique();
            $table->string('category', 50); // transport, repairs, office, etc.
            $table->decimal('amount', 12, 2);
            $table->text('description');
            $table->enum('status', ['draft', 'approved', 'rejected'])->default('draft');
            $table->uuid('requested_by');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->foreign('requested_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petty_cash_expenses');
    }
};
