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
        Schema::table('rental_item_wishlists', function (Blueprint $table) {
            $table->foreign(['user_id'], 'fk_rental_item_wishlists_user_id')->references(['id'])->on('app_users')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_item_wishlists', function (Blueprint $table) {
            $table->dropForeign('fk_rental_item_wishlists_user_id');
        });
    }
};
