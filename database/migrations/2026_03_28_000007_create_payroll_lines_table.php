<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('payroll_run_id');
            $table->uuid('user_id');                        // the staff member
            $table->string('staff_name', 150);              // snapshot
            $table->string('role', 100);                    // snapshot
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('allowances', 10, 2)->default(0);
            $table->decimal('gross_salary', 10, 2);
            $table->decimal('nssf_employee', 10, 2)->default(0);  // 5% of gross
            $table->decimal('nssf_employer', 10, 2)->default(0);  // 15% of gross
            $table->decimal('paye', 10, 2)->default(0);           // per TRA PAYE bands
            $table->decimal('other_deductions', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('payroll_run_id')
                  ->references('id')->on('payroll_runs')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_lines');
    }
};
