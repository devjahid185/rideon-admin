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
        Schema::table('make_type_relation', function (Blueprint $table) {
            $table->foreign(['make_id'], 'fk_make_type_make_id')->references(['id'])->on('rental_item_make')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['type_id'], 'fk_make_type_type_id')->references(['id'])->on('rental_item_types')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('make_type_relation', function (Blueprint $table) {
            $table->dropForeign('fk_make_type_make_id');
            $table->dropForeign('fk_make_type_type_id');
        });
    }
};
