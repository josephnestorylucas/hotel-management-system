<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'users',
        'roles',
        'buildings',
        'floors',
        'room_types',
        'rooms',
        'reservations',
        'bookings',
        'booking_charges',
        'payments',
        'guests',
        'products',
        'stock_locations',
        'stock_levels',
        'stock_movements',
        'stock_adjustments',
        'stock_transfers',
        'internal_usage_requests',
        'store_notifications',
        'system_settings',
        'conference_halls',
        'conference_bookings',
        'conference_types',
        'organizations',
        'institutions',
        'events',
        'event_schedules',
        'event_passes',
        'event_venues',
        'event_staff',
        'attendances',
        'attendance_metrics',
        'check_ins',
        'menu_categories',
        'menu_items',
        'menu_item_ingredients',
        'menu_item_option_group',
        'menu_option_groups',
        'menu_option_values',
        'tables',
        'orders',
        'order_items',
        'room_charges',
        'laundry_tasks',
        'laundry_orders',
        'laundry_order_items',
        'laundry_services',
        'laundry_service_items',
        'checkouts',
        'finance_payments',
        'payment_items',
        'financial_transactions',
        'accounts',
        'journal_entries',
        'journal_lines',
        'invoices',
        'invoice_lines',
        'payroll_runs',
        'payroll_lines',
        'bank_reconciliations',
        'petty_cash_expenses',
        'suppliers',
        'local_purchase_orders',
        'local_purchase_order_items',
        'goods_received_notes',
        'goods_received_note_items',
        'supplier_payables',
        'supplier_payments',
        'supplier_payment_allocations',
        'walkin_transactions',
        'receipts',
        'broadcasts',
        'discount_audits',
        'loyalty_transactions',
        'buffet_packages',
        'buffet_sales',
        'buffet_package_menu_item',
        'kitchen_stock_items',
        'kitchen_stock_movements',
        'kitchen_tickets',
        'bar_tickets',
        'bar_damage_reports',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'is_deleted')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->index('is_deleted');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropIndex(['is_deleted']);
                });
            }
        }
    }
};
