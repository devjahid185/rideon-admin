<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('vendor_wallets')
            ->join('food_orders', 'vendor_wallets.booking_id', '=', 'food_orders.id')
            ->where('vendor_wallets.type', 'credit')
            ->where('vendor_wallets.description', 'like', 'Restaurant food order earning%')
            ->update([
                'vendor_wallets.amount' => DB::raw('GREATEST((food_orders.items_subtotal - food_orders.tax_amount - food_orders.discount_amount), 0)'),
                'vendor_wallets.updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('vendor_wallets')
            ->join('food_orders', 'vendor_wallets.booking_id', '=', 'food_orders.id')
            ->where('vendor_wallets.type', 'credit')
            ->where('vendor_wallets.description', 'like', 'Restaurant food order earning%')
            ->update([
                'vendor_wallets.amount' => DB::raw('GREATEST((food_orders.items_subtotal + food_orders.tax_amount - food_orders.discount_amount), 0)'),
                'vendor_wallets.updated_at' => now(),
            ]);
    }
};
