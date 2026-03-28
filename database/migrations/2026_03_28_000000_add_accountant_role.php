<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        Role::updateOrCreate(
            ['name' => 'ACCOUNTANT'],
            ['description' => 'Full accounting, financial reports, payroll, bank reconciliation']
        );
    }

    public function down(): void
    {
        Role::where('name', 'ACCOUNTANT')->delete();
    }
};
