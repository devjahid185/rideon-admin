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
        Schema::create('item_city_fare', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_type_id')->index('item_type_id');
            $table->decimal('min_fare', 10)->nullable();
            $table->decimal('max_fare', 10)->nullable();
            $table->decimal('recommended_fare', 10);
            $table->decimal('admin_commission', 5);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_city_fare');
    }
};
