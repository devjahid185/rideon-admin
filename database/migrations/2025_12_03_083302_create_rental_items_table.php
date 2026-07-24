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
        Schema::create('rental_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('token', 191)->nullable()->index('token');
            $table->string('title')->nullable();
            $table->double('item_rating', 15, 2)->nullable()->default(0);
            $table->decimal('average_speed_kmph', 15)->default(40);
            $table->double('longitude', null, 0)->nullable();
            $table->double('latitude', null, 0)->nullable();
            $table->unsignedBigInteger('userid_id')->nullable()->index('userid_fk_8656820');
            $table->unsignedBigInteger('item_type_id')->nullable()->index('property_type_fk_8657403');
            $table->unsignedBigInteger('place_id')->nullable()->index('place_fk_8657368');
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('service_type', 25)->nullable();
            $table->integer('module')->nullable()->default(2);
            $table->tinyInteger('is_featured')->nullable()->default(0);
            $table->boolean('is_verified')->nullable()->default(false);
            $table->boolean('status')->nullable()->default(false);
            $table->integer('views_count')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['item_type_id'], 'property_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_items');
    }
};
