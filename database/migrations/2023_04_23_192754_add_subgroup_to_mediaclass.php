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
        Schema::table('mediaclass', function (Blueprint $table) {
            $table->string('subgroup')->after('group')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mediaclass', function (Blueprint $table) {
            $table->dropColumn('subgroup');
        });
    }
};
