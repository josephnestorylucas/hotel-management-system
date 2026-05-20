<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE checkouts MODIFY COLUMN status VARCHAR(30) DEFAULT 'pending'");
            return;
        }

        if ($driver !== 'sqlite') {
            return;
        }

        // SQLite: rebuild checkouts without enum CHECK constraint on status
        $isSqlite = $driver === 'sqlite';
        if ($isSqlite) {
            DB::statement('PRAGMA foreign_keys = OFF');
        }

        DB::transaction(function () {
            // Snapshot dependent data before dropping
            $financePaymentRows = DB::select('SELECT * FROM finance_payments');
            $bookingChargeRows  = DB::select('SELECT * FROM booking_charges');

            DB::statement('DROP TABLE IF EXISTS finance_payments');
            DB::statement('DROP TABLE IF EXISTS booking_charges');
            DB::statement('DROP TABLE IF EXISTS checkouts');

            // Recreate checkouts — status is VARCHAR (no CHECK), model validates
            DB::statement("
                CREATE TABLE checkouts (
                    id varchar not null,
                    booking_id varchar not null,
                    receipt_number varchar not null,
                    status varchar not null default 'pending',
                    total_charges_usd numeric not null default '0',
                    discount_usd numeric not null default '0',
                    grand_total_usd numeric not null default '0',
                    exchange_rate numeric default '2500',
                    grand_total_tzs numeric not null default '0',
                    paid_cash_usd numeric not null default '0',
                    paid_card_usd numeric not null default '0',
                    paid_cash_tzs numeric not null default '0',
                    paid_card_tzs numeric not null default '0',
                    total_paid_usd numeric not null default '0',
                    change_due_usd numeric not null default '0',
                    payment_method varchar,
                    notes text,
                    initiated_by varchar,
                    completed_by varchar,
                    completed_at datetime,
                    created_at datetime,
                    updated_at datetime,
                    foreign key (booking_id) references bookings(id),
                    primary key (id)
                )
            ");

            // Recreate finance_payments with exact original schema
            DB::statement("
                CREATE TABLE finance_payments (
                    id varchar not null,
                    payment_number varchar not null,
                    payment_type varchar check (payment_type in ('checkout','walkin','advance')) not null,
                    checkout_id varchar,
                    order_id varchar,
                    booking_id varchar,
                    currency varchar check (currency in ('USD','TZS')) not null,
                    amount numeric not null,
                    amount_usd numeric not null,
                    exchange_rate numeric not null default '1',
                    method varchar check (method in ('cash','card','mobile_money','bank_transfer')) not null,
                    status varchar check (status in ('pending','completed','failed','refunded')) not null default 'pending',
                    reference varchar,
                    notes text,
                    created_by varchar not null,
                    paid_at datetime,
                    created_at datetime,
                    updated_at datetime,
                    foreign key (checkout_id) references checkouts(id) on delete set null,
                    foreign key (created_by) references users(id),
                    primary key (id)
                )
            ");

            // Recreate booking_charges with exact original schema
            DB::statement("
                CREATE TABLE booking_charges (
                    id varchar not null,
                    booking_id varchar not null,
                    charge_type varchar not null,
                    reference_id varchar,
                    description varchar not null,
                    amount numeric not null,
                    status varchar not null default 'unpaid',
                    created_at datetime,
                    updated_at datetime,
                    order_id varchar,
                    created_by varchar,
                    source varchar not null default 'hotel',
                    currency varchar check (currency in ('USD','TZS')) not null default 'USD',
                    amount_tzs numeric not null default '0',
                    checkout_id varchar,
                    foreign key (booking_id) references bookings(id) on delete cascade,
                    foreign key (order_id) references orders(id) on delete set null,
                    foreign key (created_by) references users(id) on delete set null,
                    primary key (id)
                )
            ");

            // Restore data
            foreach ($financePaymentRows as $row) {
                DB::table('finance_payments')->insert((array) $row);
            }
            foreach ($bookingChargeRows as $row) {
                DB::table('booking_charges')->insert((array) $row);
            }
        });

        if ($isSqlite) {
            DB::statement('PRAGMA foreign_keys = ON');
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE checkouts MODIFY COLUMN status ENUM('pending','processing','completed','cancelled') DEFAULT 'pending'");
        }
    }
};
