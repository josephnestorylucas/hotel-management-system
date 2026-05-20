<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Fix the laundry_orders status CHECK constraint on SQLite to include 'charged'.
     * SQLite does not support ALTER TABLE MODIFY COLUMN, so we must:
     *   1. Create a new table with the updated status enum
     *   2. Copy all data across
     *   3. Drop the old table
     *   4. Rename the new table
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            // MySQL already handled by the 2026_04_03 migration
            return;
        }

        if ($driver !== 'sqlite') {
            return;
        }

        // SQLite workaround: rebuild table with expanded status check
        $isSqlite = $driver === 'sqlite';
        if ($isSqlite) {
            DB::statement('PRAGMA foreign_keys = OFF');
        }

        DB::transaction(function () {
            // 1. Create new table with 'charged' in the status enum
            DB::statement("
                CREATE TABLE laundry_orders_new (
                    id varchar not null,
                    order_number varchar not null,
                    customer_type varchar check (customer_type in ('guest', 'walkin')) not null,
                    booking_id varchar,
                    room_number varchar,
                    customer_name varchar,
                    customer_phone varchar,
                    status varchar check (status in ('received', 'processing', 'ready', 'delivered', 'collected', 'settled', 'cancelled', 'charged')) not null default 'received',
                    special_instructions text,
                    subtotal numeric not null default '0',
                    discount numeric not null default '0',
                    total numeric not null default '0',
                    payment_method varchar check (payment_method in ('cash', 'card', 'charge_to_booking')),
                    expected_ready_at datetime,
                    ready_at datetime,
                    delivered_at datetime,
                    collected_at datetime,
                    settled_at datetime,
                    received_by varchar not null,
                    processed_by varchar,
                    delivered_by varchar,
                    settled_by varchar,
                    created_at datetime,
                    updated_at datetime,
                    foreign key (received_by) references users(id),
                    primary key (id)
                )
            ");

            // 2. Copy all existing rows
            DB::statement("
                INSERT INTO laundry_orders_new
                SELECT * FROM laundry_orders
            ");

            // 3. Drop dependent table first (laundry_order_items references laundry_orders with cascade)
            DB::statement('DROP TABLE laundry_order_items');
            DB::statement('DROP TABLE laundry_orders');

            // 4. Rename new table to replace the old one
            DB::statement('ALTER TABLE laundry_orders_new RENAME TO laundry_orders');

            // 5. Recreate laundry_order_items
            DB::statement("
                CREATE TABLE laundry_order_items (
                    id varchar not null,
                    laundry_order_id varchar not null,
                    laundry_service_item_id varchar not null,
                    quantity integer not null,
                    unit_price numeric not null,
                    subtotal numeric not null,
                    notes text,
                    created_at datetime,
                    updated_at datetime,
                    foreign key (laundry_order_id) references laundry_orders(id) on delete cascade,
                    foreign key (laundry_service_item_id) references laundry_service_items(id),
                    primary key (id)
                )
            ");
        });

        if ($isSqlite) {
            DB::statement('PRAGMA foreign_keys = ON');
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            return;
        }

        if ($driver !== 'sqlite') {
            return;
        }

        // Revert: rebuild without 'charged'
        $isSqlite = $driver === 'sqlite';
        if ($isSqlite) {
            DB::statement('PRAGMA foreign_keys = OFF');
        }

        DB::transaction(function () {
            DB::statement("
                CREATE TABLE laundry_orders_old (
                    id varchar not null,
                    order_number varchar not null,
                    customer_type varchar check (customer_type in ('guest', 'walkin')) not null,
                    booking_id varchar,
                    room_number varchar,
                    customer_name varchar,
                    customer_phone varchar,
                    status varchar check (status in ('received', 'processing', 'ready', 'delivered', 'collected', 'settled', 'cancelled')) not null default 'received',
                    special_instructions text,
                    subtotal numeric not null default '0',
                    discount numeric not null default '0',
                    total numeric not null default '0',
                    payment_method varchar check (payment_method in ('cash', 'card', 'charge_to_booking')),
                    expected_ready_at datetime,
                    ready_at datetime,
                    delivered_at datetime,
                    collected_at datetime,
                    settled_at datetime,
                    received_by varchar not null,
                    processed_by varchar,
                    delivered_by varchar,
                    settled_by varchar,
                    created_at datetime,
                    updated_at datetime,
                    foreign key (received_by) references users(id),
                    primary key (id)
                )
            ");

            DB::statement("
                INSERT INTO laundry_orders_old
                SELECT * FROM laundry_orders
            ");

            DB::statement('DROP TABLE laundry_order_items');
            DB::statement('DROP TABLE laundry_orders');
            DB::statement('ALTER TABLE laundry_orders_old RENAME TO laundry_orders');

            DB::statement("
                CREATE TABLE laundry_order_items (
                    id varchar not null,
                    laundry_order_id varchar not null,
                    laundry_service_item_id varchar not null,
                    quantity integer not null,
                    unit_price numeric not null,
                    subtotal numeric not null,
                    notes text,
                    created_at datetime,
                    updated_at datetime,
                    foreign key (laundry_order_id) references laundry_orders(id) on delete cascade,
                    foreign key (laundry_service_item_id) references laundry_service_items(id),
                    primary key (id)
                )
            ");
        });

        if ($isSqlite) {
            DB::statement('PRAGMA foreign_keys = ON');
        }
    }
};
