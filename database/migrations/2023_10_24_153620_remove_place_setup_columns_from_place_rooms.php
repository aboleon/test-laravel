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
        Schema::table('place_rooms', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('capacity');
            $table->dropColumn('information');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('place_rooms', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->integer('capacity')->unsigned()->nullable();
            $table->text('information')->nullable();
        });
    }
};
