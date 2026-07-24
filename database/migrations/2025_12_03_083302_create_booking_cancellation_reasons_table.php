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
        Schema::create('booking_cancellation_reasons', function (Blueprint $table) {
            $table->integer('order_cancellation_id', true);
            $table->string('reason');
            $table->string('user_type');
            $table->boolean('status')->default(true);
            $table->boolean('module')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_cancellation_reasons');
    }
};
