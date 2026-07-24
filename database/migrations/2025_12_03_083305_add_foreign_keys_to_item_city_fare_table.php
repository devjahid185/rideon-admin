<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('item_city_fare', function (Blueprint $table) {
            $table->foreign(['item_type_id'], 'item_city_fare_ibfk_1')->references(['id'])->on('rental_item_types')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_city_fare', function (Blueprint $table) {
            $table->dropForeign('item_city_fare_ibfk_1');
        });
    }
};
