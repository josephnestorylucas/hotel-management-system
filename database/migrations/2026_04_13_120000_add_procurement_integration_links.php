<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->uuid('supplier_id')->nullable()->after('source_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();
            $table->index(['source', 'source_id']);
        });

        Schema::table('local_purchase_order_items', function (Blueprint $table) {
            $table->decimal('received_quantity', 10, 3)->default(0)->after('quantity');
        });

        Schema::table('goods_received_notes', function (Blueprint $table) {
            $table->uuid('accounting_journal_entry_id')->nullable()->after('receipt_path');
            $table->foreign('accounting_journal_entry_id')->references('id')->on('journal_entries')->nullOnDelete();
        });

        Schema::table('goods_received_note_items', function (Blueprint $table) {
            $table->uuid('stock_movement_id')->nullable()->after('product_id');
            $table->foreign('stock_movement_id')->references('id')->on('stock_movements')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('goods_received_note_items', function (Blueprint $table) {
            $table->dropForeign(['stock_movement_id']);
            $table->dropColumn('stock_movement_id');
        });

        Schema::table('goods_received_notes', function (Blueprint $table) {
            $table->dropForeign(['accounting_journal_entry_id']);
            $table->dropColumn('accounting_journal_entry_id');
        });

        Schema::table('local_purchase_order_items', function (Blueprint $table) {
            $table->dropColumn('received_quantity');
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropIndex(['source', 'source_id']);
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
        });
    }
};
