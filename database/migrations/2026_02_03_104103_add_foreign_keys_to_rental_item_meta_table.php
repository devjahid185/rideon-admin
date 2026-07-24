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
        Schema::table('rental_item_meta', function (Blueprint $table) {
            $table->foreign(['item_id'], 'fk_rental_item_meta_item_id')->references(['id'])->on('rental_items')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['item_id'], 'rental_item_meta_ibfk_1')->references(['id'])->on('rental_items')->onUpdate('cascade')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_item_meta', function (Blueprint $table) {
            $table->dropForeign('fk_rental_item_meta_item_id');
            $table->dropForeign('rental_item_meta_ibfk_1');
        });
    }
};
