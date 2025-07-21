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
            $table->foreignId('establishment_id')->nullable()->constrained('establishments')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_profile', function (Blueprint $table) {
            $table->dropForeign('account_profile_establishment_id_foreign');
            $table->dropColumn('establishment_id');
        });
    }
};
