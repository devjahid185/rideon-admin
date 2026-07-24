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
        Schema::create('reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('bookingid')->index('fk_reviews_bookingid');
            $table->unsignedBigInteger('item_id')->nullable()->default(0);
            $table->longText('item_name')->nullable();
            $table->string('guestid');
            $table->string('guest_name')->nullable();
            $table->integer('hostid')->nullable();
            $table->string('host_name')->nullable();
            $table->integer('guest_rating')->default(0);
            $table->longText('guest_message')->nullable();
            $table->integer('host_rating')->nullable()->default(0);
            $table->longText('host_message')->nullable();
            $table->integer('module')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
