<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE laundry_orders MODIFY COLUMN status ENUM('received', 'processing', 'pending_confirmation', 'ready', 'delivered', 'collected', 'settled', 'cancelled', 'charged') DEFAULT 'received'");

            Schema::table('laundry_orders', function (Blueprint $table) {
                $table->uuid('confirmed_by')->nullable()->after('settled_by');
                $table->timestamp('confirmed_at')->nullable()->after('settled_at');
                $table->foreign('confirmed_by')->references('id')->on('users');
            });
            return;
        }

        if ($driver === 'pgsql') {
            Schema::table('laundry_orders', function (Blueprint $table) {
                $table->uuid('confirmed_by')->nullable();
                $table->timestamp('confirmed_at')->nullable();
                $table->foreign('confirmed_by')->references('id')->on('users');
            });

            return;
        }

        // SQLite: rebuild table
        $isSqlite = $driver === 'sqlite';
        if ($isSqlite) {
            DB::statement('PRAGMA foreign_keys = OFF');
        }

        DB::transaction(function () {
            DB::statement("
                CREATE TABLE laundry_orders_new (
                    id varchar not null,
                    order_number varchar not null,
                    customer_type varchar check (customer_type in ('guest', 'walkin')) not null,
                    booking_id varchar,
                    room_number varchar,
                    customer_name varchar,
                    customer_phone varchar,
                    status varchar check (status in ('received', 'processing', 'pending_confirmation', 'ready', 'delivered', 'collected', 'settled', 'cancelled', 'charged')) not null default 'received',
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
                    confirmed_at datetime,
                    received_by varchar not null,
                    processed_by varchar,
                    delivered_by varchar,
                    settled_by varchar,
                    confirmed_by varchar,
                    created_at datetime,
                    updated_at datetime,
                    foreign key (received_by) references users(id),
                    foreign key (confirmed_by) references users(id),
                    primary key (id)
                )
            ");

            DB::statement("
                INSERT INTO laundry_orders_new (
                    id, order_number, customer_type, booking_id, room_number,
                    customer_name, customer_phone, status, special_instructions,
                    subtotal, discount, total, payment_method,
                    expected_ready_at, ready_at, delivered_at, collected_at, settled_at,
                    received_by, processed_by, delivered_by, settled_by,
                    created_at, updated_at
                )
                SELECT
                    id, order_number, customer_type, booking_id, room_number,
                    customer_name, customer_phone, status, special_instructions,
                    subtotal, discount, total, payment_method,
                    expected_ready_at, ready_at, delivered_at, collected_at, settled_at,
                    received_by, processed_by, delivered_by, settled_by,
                    created_at, updated_at
                FROM laundry_orders
            ");

            DB::statement('DROP TABLE laundry_order_items');
            DB::statement('DROP TABLE laundry_orders');
            DB::statement('ALTER TABLE laundry_orders_new RENAME TO laundry_orders');

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
            Schema::table('laundry_orders', function (Blueprint $table) {
                $table->dropForeign(['confirmed_by']);
                $table->dropColumn(['confirmed_by', 'confirmed_at']);
            });

            DB::statement("ALTER TABLE laundry_orders MODIFY COLUMN status ENUM('received', 'processing', 'ready', 'delivered', 'collected', 'settled', 'cancelled', 'charged') DEFAULT 'received'");
            return;
        }

        if ($driver === 'pgsql') {
            Schema::table('laundry_orders', function (Blueprint $table) {
                $table->dropForeign(['confirmed_by']);
                $table->dropColumn(['confirmed_by', 'confirmed_at']);
            });

            return;
        }

        // SQLite: rebuild without new columns
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

            DB::statement("
                INSERT INTO laundry_orders_old (
                    id, order_number, customer_type, booking_id, room_number,
                    customer_name, customer_phone, status, special_instructions,
                    subtotal, discount, total, payment_method,
                    expected_ready_at, ready_at, delivered_at, collected_at, settled_at,
                    received_by, processed_by, delivered_by, settled_by,
                    created_at, updated_at
                )
                SELECT
                    id, order_number, customer_type, booking_id, room_number,
                    customer_name, customer_phone, status, special_instructions,
                    subtotal, discount, total, payment_method,
                    expected_ready_at, ready_at, delivered_at, collected_at, settled_at,
                    received_by, processed_by, delivered_by, settled_by,
                    created_at, updated_at
                FROM laundry_orders
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
