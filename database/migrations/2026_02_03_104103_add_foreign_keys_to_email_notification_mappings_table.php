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
        Schema::table('email_notification_mappings', function (Blueprint $table) {
            $table->foreign(['email_sms_notification_id'], 'fk_email_sms_notification')->references(['id'])->on('email_sms_notification')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['email_type_id'], 'fk_email_type')->references(['id'])->on('email_type')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_notification_mappings', function (Blueprint $table) {
            $table->dropForeign('fk_email_sms_notification');
            $table->dropForeign('fk_email_type');
        });
    }
};
