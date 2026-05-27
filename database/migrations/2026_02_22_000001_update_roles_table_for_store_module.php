<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        // 1. Rename display_name → description
        if (Schema::hasColumn('roles', 'display_name')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->renameColumn('display_name', 'description');
            });
        }

        if ($driver !== 'sqlite') {
            Schema::table('roles', function (Blueprint $table) {
                $table->text('description')->nullable()->change();
            });
        }

        // 2. Rename 'manager' role → 'store_manager'
        DB::table('roles')->where('name', 'manager')->update([
            'name'        => 'store_manager',
            'description' => 'Full store control — products, pricing, stock, reports',
        ]);

        // 3. Update descriptions for existing roles
        DB::table('roles')->where('name', 'admin')->update([
            'description' => 'System administrator with full access',
        ]);
        DB::table('roles')->where('name', 'front_desk')->update([
            'description' => 'Booking and guest charges',
        ]);
        DB::table('roles')->where('name', 'supervisor')->update([
            'description' => 'Approvals and oversight',
        ]);
        DB::table('roles')->where('name', 'house_help')->update([
            'description' => 'Internal usage requests',
        ]);
        DB::table('roles')->where('name', 'store_keeper')->update([
            'description' => 'Stock operations — receive, count, fulfill',
        ]);
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        DB::table('roles')->where('name', 'store_manager')->update([
            'name' => 'manager',
        ]);

        if ($driver !== 'sqlite') {
            Schema::table('roles', function (Blueprint $table) {
                $table->string('description')->change();
            });
        }

        if (Schema::hasColumn('roles', 'description')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->renameColumn('description', 'display_name');
            });
        }
    }
};
