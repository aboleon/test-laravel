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
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn('comission');
        });

        Schema::table('event_accommodation', function (Blueprint $table) {
            $table->unsignedInteger('comission')->default(0)->after('processing_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_accommodation', function (Blueprint $table) {
            $table->dropColumn('comission');
        });
    }
};
