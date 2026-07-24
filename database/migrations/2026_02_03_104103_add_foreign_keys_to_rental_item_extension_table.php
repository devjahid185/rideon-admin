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
        Schema::table('rental_item_extension', function (Blueprint $table) {
            $table->foreign(['item_id'], 'fk_rental_item_extension_item_id')->references(['id'])->on('rental_items')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_item_extension', function (Blueprint $table) {
            $table->dropForeign('fk_rental_item_extension_item_id');
        });
    }
};
