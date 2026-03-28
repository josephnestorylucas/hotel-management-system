<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference_no', 30)->unique();    // BNK-REC-2024-03
            $table->uuid('account_id');                      // the bank account being reconciled
            $table->string('period_month', 7);               // 2024-03
            $table->date('statement_date');
            $table->decimal('statement_opening_balance', 14, 2);
            $table->decimal('statement_closing_balance', 14, 2);
            $table->decimal('system_opening_balance', 14, 2);
            $table->decimal('system_closing_balance', 14, 2);
            $table->decimal('difference', 14, 2)->default(0); // should be 0 when reconciled
            $table->enum('status', ['open', 'reconciled'])->default('open');
            $table->text('notes')->nullable();
            $table->uuid('prepared_by');
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts');
            $table->foreign('prepared_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_reconciliations');
    }
};
