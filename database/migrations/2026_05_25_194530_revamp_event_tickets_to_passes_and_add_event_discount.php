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

        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'discount_percent')) {
                $table->decimal('discount_percent', 5, 2)->nullable()->after('total_revenue');
            }
            if (!Schema::hasColumn('events', 'discount_amount')) {
                $table->decimal('discount_amount', 15, 2)->nullable()->after('discount_percent');
            }
            if (!Schema::hasColumn('events', 'discount_reason')) {
                $table->string('discount_reason')->nullable()->after('discount_amount');
            }
            if (!Schema::hasColumn('events', 'hall_rate_total')) {
                $table->decimal('hall_rate_total', 15, 2)->default(0)->after('discount_reason');
            }
            if (!Schema::hasColumn('events', 'event_rate_total')) {
                $table->decimal('event_rate_total', 15, 2)->default(0)->after('hall_rate_total');
            }
            if (!Schema::hasColumn('events', 'subtotal')) {
                $table->decimal('subtotal', 15, 2)->default(0)->after('event_rate_total');
            }
            if (!Schema::hasColumn('events', 'grand_total')) {
                $table->decimal('grand_total', 15, 2)->default(0)->after('subtotal');
            }
        });

        Schema::table('event_tickets', function (Blueprint $table) {
            if (Schema::hasColumn('event_tickets', 'price')) {
                $table->dropColumn([
                    'price',
                    'early_bird_until',
                    'bulk_discount_percent',
                    'sale_start_date',
                    'sale_end_date',
                    'includes_guide',
                ]);
            }
        });

        if ($driver !== 'sqlite') {
            DB::statement("ALTER TABLE event_tickets ALTER COLUMN tier_type TYPE VARCHAR(255)");
            DB::statement("ALTER TABLE event_tickets ALTER COLUMN tier_type SET DEFAULT 'attendee'");
            DB::statement("UPDATE event_tickets SET tier_type = 'attendee' WHERE tier_type NOT IN ('speaker', 'moderator', 'backdoor', 'attendee')");

            try {
                DB::statement("ALTER TABLE event_tickets DROP CONSTRAINT event_tickets_tier_type_check");
            } catch (\Exception $e) {}
            DB::statement("ALTER TABLE event_tickets ADD CONSTRAINT event_tickets_tier_type_check CHECK (tier_type IN ('speaker', 'moderator', 'backdoor', 'attendee'))");
        }

        Schema::rename('event_tickets', 'event_passes');

        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'pass_type')) {
                $table->string('pass_type')->default('attendee')->after('event_ticket_id');
            }
        });

        if (Schema::hasColumn('attendances', 'event_ticket_id')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->renameColumn('event_ticket_id', 'event_pass_id');
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if (Schema::hasColumn('attendances', 'event_pass_id')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->renameColumn('event_pass_id', 'event_ticket_id');
            });
        }

        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'pass_type')) {
                $table->dropColumn('pass_type');
            }
        });

        Schema::rename('event_passes', 'event_tickets');

        if ($driver !== 'sqlite') {
            DB::statement("ALTER TABLE event_tickets DROP CONSTRAINT event_tickets_tier_type_check");
            DB::statement("ALTER TABLE event_tickets ALTER COLUMN tier_type TYPE VARCHAR(255)");
            DB::statement("ALTER TABLE event_tickets ALTER COLUMN tier_type SET DEFAULT 'standard'");
        }

        Schema::table('event_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('event_tickets', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('description');
                $table->date('early_bird_until')->nullable()->after('quantity_sold');
                $table->decimal('bulk_discount_percent', 5, 2)->nullable()->after('access_type');
                $table->date('sale_start_date')->nullable()->after('bulk_discount_percent');
                $table->date('sale_end_date')->nullable()->after('sale_start_date');
                $table->boolean('includes_guide')->default(false)->after('benefits');
            }
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'discount_percent',
                'discount_amount',
                'discount_reason',
                'hall_rate_total',
                'event_rate_total',
                'subtotal',
                'grand_total',
            ]);
        });
    }
};
