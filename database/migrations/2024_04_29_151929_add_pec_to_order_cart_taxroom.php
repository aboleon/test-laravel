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
        Schema::table('order_cart_taxroom', function (Blueprint $table) {
            $table->unsignedInteger('amount_pec')->default(0)->after('amount_vat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_cart_taxroom', function (Blueprint $table) {
            $table->dropColumn('amount_pec');
        });
    }
};
