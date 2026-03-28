<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('entry_no', 30)->unique();       // JE-20240222-0001
            $table->date('entry_date');
            $table->string('reference', 200)->nullable();   // "Booking BK-001", "GRN-0042"
            $table->string('source', 50);                   // booking, restaurant, laundry, procurement, payroll, manual
            $table->uuid('source_id')->nullable();          // FK to source record (order_id, lpo_id, etc.)
            $table->text('description');
            $table->decimal('total_debit', 14, 2);
            $table->decimal('total_credit', 14, 2);
            $table->enum('status', ['draft', 'posted', 'reversed'])->default('posted');
            $table->uuid('created_by');
            $table->uuid('posted_by')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
            $table->index(['entry_date', 'source']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
