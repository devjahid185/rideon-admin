<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_order_id')->constrained('food_orders')->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->foreignId('changed_by_user_id')->nullable()->constrained('app_users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['food_order_id', 'to_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_order_status_logs');
    }
};

