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
        Schema::table('hotel_address', function (Blueprint $table) {
            $table->string('administrative_area_level_1_short', 255)->nullable()
                ->after('administrative_area_level_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotel_address', function (Blueprint $table) {
            $table->dropColumn('administrative_area_level_1_short');
        });
    }
};
