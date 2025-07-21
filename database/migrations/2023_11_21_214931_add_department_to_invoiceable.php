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
        Schema::table('order_invoiceable', function (Blueprint $table) {
            $table->text('department')->nullable();
            $table->renameColumn('adresse_line_1', 'address_line_1');
            $table->renameColumn('adresse_line_2', 'address_line_2');
            $table->renameColumn('adresse_line_3', 'address_line_3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_invoiceable', function (Blueprint $table) {
            $table->dropColumn('department');
            $table->renameColumn('address_line_1','adresse_line_1');
            $table->renameColumn('address_line_2', 'adresse_line_2');
            $table->renameColumn('address_line_3', 'adresse_line_3');
        });
    }
};
