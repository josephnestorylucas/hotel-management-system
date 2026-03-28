<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference_no', 30)->unique();   // PAY-2024-03
            $table->string('period_month', 7);              // 2024-03 (YYYY-MM)
            $table->date('pay_date');
            $table->decimal('total_gross', 12, 2)->default(0);
            $table->decimal('total_nssf_employee', 12, 2)->default(0);
            $table->decimal('total_nssf_employer', 12, 2)->default(0);
            $table->decimal('total_paye', 12, 2)->default(0);
            $table->decimal('total_net', 12, 2)->default(0);
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->text('notes')->nullable();
            $table->uuid('prepared_by');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('prepared_by')->references('id')->on('users');
            $table->unique('period_month');                 // one payroll run per month
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_runs');
    }
};
