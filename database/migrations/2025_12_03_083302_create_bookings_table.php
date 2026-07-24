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
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('token', 10)->nullable()->index('token');
            $table->string('itemid')->index('itemid');
            $table->string('userid')->index('userid');
            $table->bigInteger('host_id')->index('host_id');
            $table->date('ride_date')->index('check_in');
            $table->enum('status', ['Pending', 'Ongoing', 'Arrived', 'Accepted', 'Cancelled', 'Confirmed', 'Declined', 'Expired', 'Refunded', 'Completed', 'Rejected'])->index('status');
            $table->decimal('price_per_km', 15);
            $table->decimal('base_price', 15);
            $table->decimal('service_charge', 15)->nullable()->default(0);
            $table->decimal('iva_tax', 15)->nullable()->default(0);
            $table->string('coupon_code', 100)->nullable();
            $table->double('coupon_discount', 15, 2)->default(0);
            $table->double('discount_price', 15, 2)->default(0);
            $table->double('amount_to_pay', 15, 2)->nullable()->default(0);
            $table->decimal('total', 15)->nullable()->default(0);
            $table->decimal('admin_commission', 24)->default(0);
            $table->decimal('vendor_commission', 24)->default(0);
            $table->tinyInteger('vendor_commission_given')->default(0);
            $table->string('currency_code')->nullable();
            $table->string('cancellation_reasion')->nullable();
            $table->decimal('cancelled_charge', 15)->default(0);
            $table->string('transaction')->nullable();
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['notpaid', 'pending', 'paid', 'offline', ''])->nullable();
            $table->longText('firebase_json')->nullable();
            $table->decimal('wall_amt', 15)->nullable()->default(0);
            $table->integer('rating')->default(0);
            $table->tinyInteger('module')->default(2);
            $table->string('cancelled_by')->nullable();
            $table->double('deductedAmount', 15, 2)->default(0);
            $table->double('refundableAmount', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
