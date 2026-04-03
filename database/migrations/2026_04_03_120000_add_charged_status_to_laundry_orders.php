<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // SQLite does not support ALTER TABLE MODIFY COLUMN.
        // In production (MySQL), run: ALTER TABLE laundry_orders MODIFY COLUMN status ENUM(...)
        // For SQLite (dev), the column type is string-based so 'charged' will work automatically.
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE laundry_orders MODIFY COLUMN status ENUM('received', 'processing', 'ready', 'delivered', 'collected', 'settled', 'cancelled', 'charged') DEFAULT 'received'");
        }
        // For SQLite: no action needed — column is VARCHAR and accepts any string value.
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("UPDATE laundry_orders SET status = 'received' WHERE status = 'charged'");
            DB::statement("ALTER TABLE laundry_orders MODIFY COLUMN status ENUM('received', 'processing', 'ready', 'delivered', 'collected', 'settled', 'cancelled') DEFAULT 'received'");
        }
    }
};
