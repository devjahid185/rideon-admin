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
        Schema::create('rental_item_type_service_type', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('rental_item_type_id');
            $table->unsignedBigInteger('rental_item_service_type_id')->index('rental_item_service_type_id');
            $table->timestamps();

            $table->unique(['rental_item_type_id', 'rental_item_service_type_id'], 'uniq_item_service');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_item_type_service_type');
    }
};
