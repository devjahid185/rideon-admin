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
        Schema::create('rental_item_extension', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->unsignedBigInteger('item_id')->unique('item_id');
            $table->string('year');
            $table->string('color', 50)->nullable();
            $table->string('vehicle_registration_number')->nullable();
            $table->string('odometer', 50)->nullable();
            $table->string('transmission', 50)->nullable();

            $table->index(['item_id'], 'item_id_2');
            $table->unique(['item_id'], 'item_id_3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_item_extension');
    }
};
