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
        Schema::table('vendor_wallets', function (Blueprint $table) {
            $table->foreign(['vendor_id'], 'fk_vendor_wallets_vendor_id')->references(['id'])->on('app_users')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_wallets', function (Blueprint $table) {
            $table->dropForeign('fk_vendor_wallets_vendor_id');
        });
    }
};
