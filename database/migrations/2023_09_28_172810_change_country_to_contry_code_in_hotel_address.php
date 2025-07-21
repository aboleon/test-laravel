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

            $table->string('country',2)->change();
            $table->renameColumn('country', 'country_code');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotel_address', function (Blueprint $table) {

            $table->string('country_code',255)->change();
            $table->renameColumn('country_code', 'country');
        });
    }
};
