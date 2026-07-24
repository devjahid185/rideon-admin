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
        Schema::table('booking_meta', function (Blueprint $table) {
            $table->foreign(['booking_id'], 'fk_booking_meta_booking_id')->references(['id'])->on('bookings')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_meta', function (Blueprint $table) {
            $table->dropForeign('fk_booking_meta_booking_id');
        });
    }
};
