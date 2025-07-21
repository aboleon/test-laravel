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
        Schema::table('group_address', function (Blueprint $table) {

            $table->text('prefix')->nullable();
            $table->string('cedex')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_address', function (Blueprint $table) {

            $table->dropColumn('prefix');
            $table->dropColumn('cedex');
        });
    }
};
