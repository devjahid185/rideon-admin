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
        Schema::create('email_notification_mappings', function (Blueprint $table) {
            $table->unsignedInteger('email_type_id');
            $table->unsignedInteger('email_sms_notification_id')->index('email_sms_notification_id');
            $table->unsignedInteger('module');

            $table->unique(['email_type_id', 'email_sms_notification_id', 'module'], 'email_type_id');
            $table->primary(['email_type_id', 'email_sms_notification_id', 'module']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_notification_mappings');
    }
};
