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
        Schema::create('booking_extensions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('booking_id')->index('booking_id');
            $table->string('ride_id')->nullable();
            $table->longText('pickup_location')->nullable();
            $table->longText('dropoff_location')->nullable();
            $table->decimal('estimated_distance_km', 10)->nullable();
            $table->integer('estimated_duration_min')->nullable();
            $table->string('pick_otp', 10)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_extensions');
    }
};
