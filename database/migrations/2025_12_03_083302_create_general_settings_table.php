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
        Schema::create('general_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('meta_key')->unique('meta_key');
            $table->text('meta_value');
            $table->tinyInteger('module')->default(1)->index('module');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['meta_key'], 'meta_key_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
