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
        Schema::table('account_address', function (Blueprint $table) {
            $table->renameColumn('prefix', 'complementary');
            $table->dropColumn('administrative_area_level_1');
            $table->dropColumn('administrative_area_level_1_short');
            $table->dropColumn('administrative_area_level_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_address', function (Blueprint $table) {
            $table->renameColumn('complementary','prefix');
            $table->string('administrative_area_level_1');
            $table->string('administrative_area_level_1_short');
            $table->string('administrative_area_level_2');
        });
    }
};
