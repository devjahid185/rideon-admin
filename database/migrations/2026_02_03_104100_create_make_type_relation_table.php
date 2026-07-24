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
        Schema::create('make_type_relation', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('make_id')->index('fk_make_type_make_id');
            $table->unsignedBigInteger('type_id')->nullable()->index('fk_make_type_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('make_type_relation');
    }
};
