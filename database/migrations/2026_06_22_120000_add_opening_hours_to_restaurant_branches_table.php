<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurant_branches', function (Blueprint $table) {
            if (! Schema::hasColumn('restaurant_branches', 'opening_time')) {
                $table->time('opening_time')->nullable()->after('max_delivery_time_minutes');
            }

            if (! Schema::hasColumn('restaurant_branches', 'closing_time')) {
                $table->time('closing_time')->nullable()->after('opening_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_branches', function (Blueprint $table) {
            if (Schema::hasColumn('restaurant_branches', 'closing_time')) {
                $table->dropColumn('closing_time');
            }

            if (Schema::hasColumn('restaurant_branches', 'opening_time')) {
                $table->dropColumn('opening_time');
            }
        });
    }
};
