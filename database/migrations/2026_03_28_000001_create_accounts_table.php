<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 20)->unique();           // 4100, 6100, etc.
            $table->string('name', 150);                    // Room Revenue, Cash on Hand
            $table->enum('type', [
                'asset',
                'liability',
                'equity',
                'revenue',
                'expense',
                'cogs',                                     // cost of goods sold
            ]);
            $table->enum('normal_balance', ['debit', 'credit']); // asset/expense = debit, others = credit
            $table->uuid('parent_id')->nullable();          // for account grouping
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);   // system accounts cannot be deleted
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
