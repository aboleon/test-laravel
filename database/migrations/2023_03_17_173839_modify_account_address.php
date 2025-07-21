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
            if (Schema::hasColumn('account_address', 'establishment_id')) {
                $table->dropForeign('account_address_establishment_id_foreign');
                $table->dropColumn('establishment_id');
            }
            $table->string('company')->nullable();
            $table->text('prefix')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_address', function (Blueprint $table) {
            $table->dropColumn('company');
            $table->dropColumn('prefix');
        });
    }
};
