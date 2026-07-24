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
            if (! Schema::hasColumn('food_orders', 'delivery_otp')) {
                $table->string('delivery_otp', 10)->nullable()->after('customer_phone_country');
            }
        });

        DB::table('food_orders')
            ->whereNull('delivery_otp')
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->orderBy('id')
            ->chunkById(100, function ($orders) {
                foreach ($orders as $order) {
                    DB::table('food_orders')
                        ->where('id', $order->id)
                        ->update(['delivery_otp' => (string) random_int(1000, 9999)]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('food_orders', function (Blueprint $table) {
            if (Schema::hasColumn('food_orders', 'delivery_otp')) {
                $table->dropColumn('delivery_otp');
            }
        });
    }
};
