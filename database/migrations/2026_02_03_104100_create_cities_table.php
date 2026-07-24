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
        Schema::create('cities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('city_name');
            $table->string('country_code', 20);
            $table->longText('description')->nullable();
            $table->string('status')->nullable();
            $table->string('latitude', 20)->nullable();
            $table->string('longtitude', 20);
            $table->string('region', 250)->nullable();
            $table->boolean('module')->nullable()->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
