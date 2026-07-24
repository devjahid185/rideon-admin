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
        Schema::create('email_sms_notification', function (Blueprint $table) {
            $table->increments('id');
            $table->string('temp_name', 250);
            $table->tinyInteger('module')->default(1);
            $table->string('role', 250);
            $table->string('subject', 191);
            $table->text('body');
            $table->string('link_text', 191)->nullable();
            $table->string('lang', 10);
            $table->integer('lang_id')->nullable()->default(0);
            $table->text('sms');
            $table->text('push_notification');
            $table->boolean('emailsent')->nullable()->default(true);
            $table->boolean('smssent')->default(true);
            $table->boolean('pushsent')->default(true);
            $table->string('vendorsubject', 91)->nullable();
            $table->text('vendorbody')->nullable();
            $table->text('vendorpush_notification')->nullable();
            $table->tinyInteger('vendoremailsent')->default(0);
            $table->tinyInteger('vendorsmssent')->default(0);
            $table->tinyInteger('vendorpushsent')->default(0);
            $table->text('vendorsms')->nullable();
            $table->string('adminsubject', 99)->nullable();
            $table->text('adminbody')->nullable();
            $table->text('adminpush_notification')->nullable();
            $table->tinyInteger('adminemailsent')->default(0);
            $table->tinyInteger('adminsmssent')->default(0);
            $table->tinyInteger('adminpushsent')->default(0);
            $table->text('adminsms')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_sms_notification');
    }
};
