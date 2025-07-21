<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('account_profile', function (Blueprint $table) {
            if (false === Schema::hasColumn('account_profile', 'profession_id')) {
                $table->foreignId('profession_id')->after("domain_id")->constrained('dictionnary_entries')->cascadeOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_profile', function (Blueprint $table) {
            if (true === Schema::hasColumn('account_profile', 'profession_id')) {
                $table->dropForeign(['profession_id']);
                $table->dropColumn('profession_id');
            }
        });
    }
};
