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
        Schema::table('event_accommodation', function (Blueprint $table) {
            $table->unsignedInteger('comission_room')->default(0)->after('comission');
            $table->unsignedInteger('comission_breakfast')->default(0)->after('comission_room');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_accommodation', function (Blueprint $table) {
            $table->dropColumn('comission_room');
            $table->dropColumn('comission_breakfast');
        });
    }
};
