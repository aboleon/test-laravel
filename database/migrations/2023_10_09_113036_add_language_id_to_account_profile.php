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
        Schema::table('account_profile', function (Blueprint $table) {
            $table->foreignId('language_id')->nullable()->after('profession_id')->references('id')->on('dictionnary_entries')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_profile', function (Blueprint $table) {
            $table->dropForeign('account_profile_language_id_foreign');
            $table->dropColumn('language_id');
        });
    }
};
