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
        Schema::table('rental_item_type_service_type', function (Blueprint $table) {
            $table->foreign(['rental_item_type_id'], 'rental_item_type_service_type_ibfk_1')->references(['id'])->on('rental_item_types')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['rental_item_service_type_id'], 'rental_item_type_service_type_ibfk_2')->references(['id'])->on('rental_item_service_types')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_item_type_service_type', function (Blueprint $table) {
            $table->dropForeign('rental_item_type_service_type_ibfk_1');
            $table->dropForeign('rental_item_type_service_type_ibfk_2');
        });
    }
};
