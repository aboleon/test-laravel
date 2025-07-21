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
            $table->string('name')->nullable();
            $table->foreignId('establishment_id')->nullable()->constrained('establishments')->nullOnDelete();
            $table->string('administrative_area_level_1_short')->after('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_address', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropForeign('account_address_establishment_id_foreign');
            $table->dropColumn('establishment_id');
        });
    }
};
