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
        Schema::table('pec_distribution', function (Blueprint $table) {
            $table->unsignedInteger('sub_ht')->after('amount')->default(0);
            $table->unsignedInteger('sub_vat')->after('sub_ht')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pec_distribution', function (Blueprint $table) {
            $table->dropColumn('sub_ht');
            $table->dropColumn('sub_vat');
        });
    }
};
