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
        Schema::create('payouts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('vendorid')->index('fk_payouts_vendorid');
            $table->decimal('amount', 15);
            $table->string('currency')->nullable();
            $table->string('request_by')->nullable()->default('vendor');
            $table->string('payment_method')->nullable();
            $table->enum('payout_status', ['Pending', 'Success', 'Rejected'])->nullable();
            $table->longText('note')->nullable();
            $table->boolean('module')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
