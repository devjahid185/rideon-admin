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
        Schema::create('app_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('firestore_id')->nullable()->index('firestore_id');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('phone_country')->nullable();
            $table->string('default_country')->nullable();
            $table->string('gender')->nullable();
            $table->string('password')->nullable();
            $table->decimal('wallet', 15)->nullable();
            $table->integer('otp_value')->nullable()->default(0);
            $table->text('token')->nullable();
            $table->integer('reset_token')->nullable()->default(0);
            $table->tinyInteger('verified')->nullable();
            $table->tinyInteger('document_verify')->nullable()->default(0);
            $table->tinyInteger('phone_verify')->default(0);
            $table->tinyInteger('email_verify')->default(0);
            $table->string('login_type', 250)->nullable();
            $table->string('user_type')->default('user');
            $table->enum('host_status', ['0', '1', '2'])->nullable()->default('0');
            $table->string('social_id', 250)->nullable();
            $table->decimal('ave_host_rate', 15)->default(0);
            $table->decimal('avr_guest_rate', 15)->default(0);
            $table->boolean('status')->nullable()->default(true);
            $table->unsignedBigInteger('package_id')->nullable()->default(1)->index('package_fk_8713947');
            $table->text('fcm')->nullable();
            $table->text('device_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['email'], 'email');
            $table->unique(['phone', 'phone_country'], 'phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_users');
    }
};
