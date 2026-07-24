<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('food_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('food_orders', 'driver_commission')) {
                $table->decimal('driver_commission', 15, 2)
                    ->default(0)
                    ->after('delivery_fee');
            }

            if (! Schema::hasColumn('food_orders', 'driver_commission_credited_at')) {
                $table->timestamp('driver_commission_credited_at')
                    ->nullable()
                    ->after('driver_commission');
            }
        });

        DB::table('food_orders')
            ->where('driver_commission', 0)
            ->update([
                'driver_commission' => DB::raw('COALESCE(delivery_fee, 0)'),
            ]);
    }

    public function down(): void
    {
        Schema::table('food_orders', function (Blueprint $table) {
            if (Schema::hasColumn('food_orders', 'driver_commission_credited_at')) {
                $table->dropColumn('driver_commission_credited_at');
            }

            if (Schema::hasColumn('food_orders', 'driver_commission')) {
                $table->dropColumn('driver_commission');
            }
        });
    }
};
